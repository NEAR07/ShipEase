# AI Powered Content Generation and Multimedia Utility Website

# Features:
* Text Generation<br>
* Image Analysis<br>
* PDF to Word Converter<br>
* Text from PDF Extractor<br>
* Word to PDF Converter<br>
* QR Code Generation<br>
* Barcode Generation<br>
* URL Shortener

# Installation

1. Clone or download the repository
2. Go to the project directory and run `composer install`
3. Run `composer require simplesoftwareio/simple-qrcode "~4"` and `composer require picqer/php-barcode-generator` to add the packages
4. Run the command to generate application key `php artisan key:generate`
5. Link storage directory: `php artisan storage:link`
6. Run this command to create a table `php artisan make:migration create_url_mappings_table` and migrate using `php artisan migrate`
7. Obtain your free Gemini API key from [Get API Key.](https://makersuite.google.com/app/apikey) and paste it into the .env file in place of `your-api-key`
8. Open cmd in the project root directory and install Node using `npm i` command
9. Run the "gemini.js" server by `npm start` command from terminal or using VS Code's run feature. You will also need to install all the necessary dependencies. See "package.json" file for those
10. Run the command `php artisan serve` from the project root and visit `localhost:8000` to access the application

<h4>Note: For PDF to Word converter you will need to have Python installed</h4>

# Screenshots

![Homepage](https://github.com/NEAR07/ShipEase/blob/main/Screenshots/Home.png)
![PDF Converter](https://github.com/NEAR07/ShipEase/blob/main/Screenshots/Convert%20PDF.png)
![All Tools](https://github.com/NEAR07/ShipEase/blob/main/Screenshots/Tools.png)
 
