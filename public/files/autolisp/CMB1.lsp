(setq global-CsvPath nil) ; Variabel global untuk menyimpan path file CSV

(defun C:CMB1 () 
    (CMB1CSV) 
    (MBB) 
    (MBBCSV) 
    (princ "\nAll commands executed successfully.")
)

(defun CMB1CSV ()
    (vl-load-com)
    (defun BrowseFolder (Message / shellApp folder result)
        (setq shellApp (vlax-create-object "Shell.Application"))
        (setq folder (vlax-invoke-method shellApp 'BrowseForFolder 0 Message 0))
        (vlax-release-object shellApp)
        (if folder 
            (progn
                (setq result (vlax-get-property folder 'Self))
                (setq result (vlax-get-property result 'Path))
                (if (/= (substr result (strlen result)) "\\")
                    (setq result (strcat result "\\"))))
        )
        result
    )

    (defun ReadCSV (filename / file line data row)
        (setq data '())
        (if (findfile filename)
            (progn
                (setq file (open filename "r"))
                (read-line file) ; Skip header line
                (while (setq line (read-line file))
                    (setq row (vl-string->list line ";"))
                    (setq data (append data (list (cadr row))))
                )
                (close file))
            (alert (strcat "File tidak ditemukan: " filename)))
        data
    )

    (defun vl-string->list (str delim)
        (if (vl-string-search delim str)
            (cons (substr str 1 (vl-string-search delim str))
                  (vl-string->list (substr str (+ (vl-string-search delim str) 2)) delim))
            (list str)))

    (if (setq DirPath (BrowseFolder "Select directory to scan drawings"))
        (progn
            (setq Scrfile (strcat DirPath "tes2.scr"))
            (setq ofile (open Scrfile "w"))
            (setq DwgList (vl-directory-files DirPath "*.dxf" 1))

            ;; Cek apakah global-CsvPath sudah terisi
            (if (not global-CsvPath)
                (setq global-CsvPath (getfiled "Select CSV file for sorting" "" "csv" 16)))

            (if global-CsvPath
                (progn
                    (setq sorted-names (ReadCSV global-CsvPath))
                    (if (not sorted-names)
                        (alert "CSV file is empty or invalid.")
                        (progn
                            (setq x 0)
                            (setq count 0)
                            (foreach csv-name sorted-names
                                (foreach file DwgList
                                    (if (and csv-name (wcmatch file (strcat csv-name "*")))
                                        (progn
                                            (command "-insert" (strcat DirPath file) (list x 0.0 0.0) "" "" "")
                                            (setq x (+ x 100))
                                            (setq count (+ count 1))
                                        ))))
                            (alert (strcat "Total file DXF yang berhasil dibuka: " (itoa count)))))
                )
                (alert "No CSV file selected"))))
    (princ)
)

(defun AddToGroup (key value groups)
    (if (assoc key groups)
        (setq groups (mapcar (function
            (lambda (x)
                (if (equal (car x) key)
                    (cons key (append (cdr x) (list value)))
                    x)))
            groups))
        (setq groups (cons (cons key (list value)) groups)))
    groups
)

(defun MBB (/ ss n blk blk-name csv-data groups offsetX offsetY refX refY minpt maxpt LL UR width height scaleFactor)
    (setq A4Width 297.0
          A4Height 210.0
          offsetX 50.0  ; Jarak antar gambar
          offsetY 500.0 ; Jarak antar kelompok
          refX 0.0
          refY 0.0
          csv-data '()
          groups '())

    (if (and global-CsvPath (setq csv-data (ReadCSV global-CsvPath)))
        (progn 
            (if (setq ss (ssget "X" '((0 . "INSERT"))))
                (progn
                    (repeat (setq n (sslength ss))
                        (setq blk (ssname ss (setq n (1- n))))
                        (setq blk-name (cdr (assoc 2 (entget blk))))
                        (if (not blk-name)
                            (setq blk-name "Unknown"))
                        (foreach desc csv-data
                            (if (wcmatch blk-name (strcat desc "*"))   
                                (setq groups (AddToGroup desc blk groups)))))

                    (foreach group groups
                        (setq refX 0.0)  ; Reset refX untuk setiap grup
                        (foreach blk (cdr group)
                            (vla-getboundingbox (vlax-ename->vla-object blk) 'minpt 'maxpt)
                            (setq LL (vlax-safearray->list minpt)
                                  UR (vlax-safearray->list maxpt))
                            (setq width (- (car UR) (car LL))
                                  height (- (cadr UR) (cadr LL)))

                            (if (and (> width 0) (> height 0))
                                (progn 
                                    (setq scaleX (/ A4Width width)
                                          scaleY (/ A4Height height)
                                          scaleFactor (if (< scaleX scaleY) scaleX scaleY))

                                    (command "_.scale" blk "" LL scaleFactor)
                                    (command "_.move" blk "" LL (list refX refY))
                                    (setq refX (+ refX width offsetX))  ; Susun horizontal di sini
                                )
                            )
                        )
                    )
                )
            )
        )
        (alert "CSV file tidak dapat dibaca"))
        
    (princ)
)

(defun MBBCSV (/ ss n blk blk-name csv-data groups offsetX offsetY refX refY minpt maxpt LL UR width height scaleFactor groupWidth groupHeight titleOffsetY)
    (setq A4Width 297.0
          A4Height 210.0
          offsetX 50.0         ; Jarak vertikal antar blok dalam kelompok
          offsetY 100.0        ; Jarak horizontal antar kelompok
          titleOffsetY 50.0    ; Jarak antara blok terakhir dan judul di bawah
          refX 0.0
          refY 0.0
          csv-data '()
          groups '())

    (if (and global-CsvPath (setq csv-data (ReadCSV global-CsvPath)))
        (progn
            (if (setq ss (ssget "X" '((0 . "INSERT"))))
                (progn
                    ;; Kelompokkan blok berdasarkan CSV
                    (repeat (setq n (sslength ss))
                        (setq blk (ssname ss (setq n (1- n))))
                        (setq blk-name (cdr (assoc 2 (entget blk))))
                        (foreach desc csv-data
                            (if (wcmatch blk-name (strcat desc "*"))   
                                (setq groups (AddToGroup desc blk groups)))))

                    ;; Atur setiap kelompok secara vertikal, kelompok baru di samping
                    (foreach group groups
                        (setq refY 0.0)  ; Reset posisi Y untuk kelompok baru
                        (setq groupWidth 0.0)  ; Untuk melacak lebar maksimum kelompok
                        (setq groupHeight 0.0) ; Untuk melacak tinggi total kelompok

                        ;; Pindahkan blok dalam kelompok secara vertikal terlebih dahulu
                        (foreach blk (cdr group)
                            (vla-getboundingbox (vlax-ename->vla-object blk) 'minpt 'maxpt)
                            (setq LL (vlax-safearray->list minpt)
                                  UR (vlax-safearray->list maxpt))
                            (setq width (- (car UR) (car LL))
                                  height (- (cadr UR) (cadr LL)))

                            (if (and (> width 0) (> height 0))
                                (progn 
                                    (setq scaleX (/ A4Width width)
                                          scaleY (/ A4Height height)
                                          scaleFactor (if (< scaleX scaleY) scaleX scaleY))

                                    (command "_.scale" blk "" LL scaleFactor)
                                    (command "_.move" blk "" LL (list refX refY))  ; Pindahkan blok ke posisi baru
                                    (setq refY (+ refY height offsetX))  ; Update refY untuk blok berikutnya
                                    (setq groupHeight (+ groupHeight height offsetX))  ; Tambah tinggi kelompok

                                    ;; Perbarui lebar maksimum kelompok
                                    (if (> width groupWidth)
                                        (setq groupWidth width))
                                )
                            )
                        )

                        ;; Tambahkan judul kelompok di bawah setelah semua blok diposisikan
                        (command "_.text"
                                 (list refX (+ refY titleOffsetY))  ; Posisi teks di bawah kelompok
                                 50.0  ; Ukuran teks
                                 0.0   ; Rotasi teks
                                 (car group))  ; Judul kelompok

                        ;; Geser refX untuk kelompok berikutnya berdasarkan lebar maksimum ditambah jarak
                        (setq refX (+ refX groupWidth offsetY))  ; Jarak antar kelompok
                    )
                )
            )
        )
        (alert "CSV file tidak dapat dibaca"))

    (princ)
)