(defun c:AFC ()

    (if (not (tblsearch "style" "Autostyle"))
        (command "STYLE" "AutoStyle" "Calibri" "" "" "0" "1" "N" "N")
        (command "STYLE" "AutoStyle" "Calibri" "" "" "0" "1" "N" "N")
    )

    (setq ss (ssget "X" '((0 . "TEXT,MTEXT"))))

    (if ss
        (progn
            (repeat (sslength ss)
                (setq ent (ssname ss 0))
                (setq entData (entget ent))
		
		(if (= (cdr (assoc 62 entData)) 4)
		(progn
                    (setq entData (subst (cons 7 "AutoStyle") (assoc 7 entData) entData)) 

                (setq entData (subst (cons 40 70.0) (assoc 40 entData) entData)) ; merubah size font

                (entmod entData)
		)
	    )		
                (ssdel ent ss)
            )
            (princ "\n Sukses Merubah Font")
        )    
    )
    (princ)
)