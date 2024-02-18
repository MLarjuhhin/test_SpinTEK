# Test task for Spin TEK
* ** **
## 📝 The task

Spin TEKis makstakse töötasu iga kuu kümnendal kuupäeval. Töötasu saab maksta ainult tööpäeval, seega kui 10. kuupäev langeb nädalavahetusele või riigipühale, siis makstakse tasu välja sellele eelneval tööpäeval. Raamatupidaja soovib, et talle saadetaks meeldetuletus maksmise kohta kolm tööpäeva
enne maksmise kuupäeva.

1. Ülesandeks on kirjutada veebiteenus päringu jaoks, mille sisendiks on aastaarv (näiteks „GET /2023“) ning väljundiks valitud aasta iga kuu palgamaksmise kuupäev ja raamatupidajale meeldetuletuse saatmise kuupäev.
2. Teenus peab väljundi andma JSON-formaadis, aga väljundi täpse struktuuri saad ise välja pakkuda.
3. Palgamaksmise kuupäev peaks olema rakenduses häälestatav (näiteks kümnenda asemel kuu esimesel, viiendal või viimasel päeval). Häälestamine võib toimuda näiteks konfiguratsioonifaili kaudu, haldusliidest selleks tegema ei pea.
4. Teenus peaks olema avalik (ilma autentimata ligipääsetav).
5. Lahendus tuleb kirjutada PHP keeles. Muid piiranguid lahenduse teostamiseks ei ole.
* ** **
## 🔨 What was done

* **Retrieving data about holidays**:
The service automatically requests and receives a list of official public holidays in Estonia. This information is loaded from an external source specified in the .env settings file and saved locally in JSON format for later use.
  * Link where we get state holidays from (configurable in .env)
     ```sh
     https://xn--riigiphad-v9a.ee/
      ```
* **Data update**:
Each time the service is started, the date of the last update of the file with holidays is checked. If more than 30 days have passed since the last update, the holiday information is updated. This ensures that the data is up to date without the need to consult an external source every day.

* **Payday calculation**:
The system determines the payday based on the 'DAY_TYPE' setting in the .env file. If the scheduled payment day falls on a weekend or holiday, the payment is postponed to the previous business day.

* **Salary reminder**:
A certain number of working days before the payday, the system sends a reminder. The number of days for notification is also configurable in the .env file.
* ** **
## 📂 Project structure
* **config**: Configuration files that define application settings
* **log**: This directory contains log files where various error messages are written
* **public**: This is the publicly accessible root directory of the web server
* **scr** Application source code.
  * **Config**: Configuration class.
  * **Controller**: Controllers that control the flow of data in the application by connecting models and routes
  * **Foundation**: The basic components of the application.
    * **Http**: Class responsible for HTTP responses.
    * **Routing**: Application route definitions
  * **Interface**: Interface definitions
  * **Service**: Service classes that provide business logic
* **storage**: Folder for storing temporary data
* **tests**:Test scripts and classes
* **vendor**: Created and managed by Composer, contains third party libraries
* ** **
## ⚙️.env

| KEY                    | VALUE                                     | DESCRIPTION                                                                                                                                   |
|------------------------|-------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------|
| **API_URL**            | https://xn--riigiphad-v9a.ee/?output=json | Link holidays                                                                                                                                 |
| **DAY_TYPE**           | int (1-30) /  last_workday                | **int (1-31)** The day on which we would like to pay wages. <br> **last_workday** Salaries will be paid on the last working day of the month. |
| **NOTIFICATIONS_DAYS** | int (1-30)                                | Number of days for which salary payment reminders are sent                                                                                    |
| **EXPIRY_FILE_DAYS**   | int (1-30)                                | File lifetime in days                                                                                                                         |


* ** **
## 🔒 Installation and configuration

1.**📦Intall docker**

   ```sh
   https://docs.docker.com/engine/install/
   ```

2.**📥Clone the Repository**
```sh
git@github.com:MLarjuhhin/test_SpinTEK.git
cd test_SpinTEK
```

3.**🚀Start the docker Server**

* **docker-compose.yml**: Configures and runs Nginx and PHP, creating the basis for a web application.
* **Dockerfile**: Defines a custom image with PHP and Composer, installs the necessary PHP extensions.
* **entrypoint.sh**: Initializes the environment, creates folders, configures permissions, and runs composer install for project dependencies.

### 📖 Launch Instructions

  ```sh
   docker-compose up --build
   ```

This command runs the app.
* ** **
## ️️ 🖥️ How to use

Open http://localhost:3000/ to view it in the browser.<br>
For information go to http://localhost:3000/schedule <br>
By default, the data will be taken for the current year; if you need a specific year, then you need to specify: http://localhost:3000/schedule/2025
* ** **
## <span style="color: green;">✔</span> Json response

**If it worked correctly, we receive JSON in the response**

* **status**: success
* **data** : Inside there is an array with data.
    * **pay_day**: salary payment date in DD.MM.YYYY format
    * **notification_day**: date of sending the reminder in the format DD.MM.YYYY
* <b>HTTP code</b>:   <code>200</code>
* ** **
## <span style="color: red;">✘️</span> Error codes

**If it worked incorrectly, we will receive JSON in the response**

* **status**: error

* **HTTP code:**

| Code             | Msg                                                                                            | Description                          |
|------------------|------------------------------------------------------------------------------------------------|--------------------------------------|
| <code>500</code> | <code>Fatal error </code> or <br> <code>Service is temporarily unavailable. Try later. </code> | Unexpected error on the server   |
| <code>400</code> | <code>Invalid Year </code>                                                                     | The year specified in the URL is incorrect      |
| <code>405</code> | <code>Method Not Allowed</code>                                                                | The wrong request method is being used |
| <code>404</code> | <code>Not Found</code>                                                                         | Page not found                |
