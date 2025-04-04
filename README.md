# AI Powered Content Generation and Multimedia Utility Website

# Features:
* Merge and Manage Your PDF<br>
* Numbering PDF Eticket Nesting Profile<br>
* Convert Partlist to Excel<br>
* Partname Converter<br>
* Resume Matlist<br>
* Compare Data<br>
* Word to PDF Converter<br>
* QR Code Maker<br>
* Barcode Maker<br>
* URL Shortener

# Installation

1. Clone or download the repository
2. Go to the project directory and run `composer install`
3. Run `composer require simplesoftwareio/simple-qrcode "~4"` and `composer require picqer/php-barcode-generator` to add the packages
4. Run the command to generate application key `php artisan key:generate`
5. Link storage directory: `php artisan storage:link`
6. Run this command to create a table `php artisan make:migration create_url_mappings_table` and migrate using `php artisan migrate`
7. Run the command `php artisan serve` from the project root and visit `localhost:8000` to access the application

<h4>Note: For PDF to Word converter you will need to have Python installed</h4>

# Screenshots

![Homepage](https://github.com/NEAR07/ShipEase/blob/april/Screenshots/Home.png)
![PDF Converter](https://github.com/NEAR07/ShipEase/blob/april/Screenshots/Convert%20PDF.png)
![All Tools](https://github.com/NEAR07/ShipEase/blob/april/Screenshots/Tools.png)
 
