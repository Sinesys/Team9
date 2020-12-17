# Team 9 - MOPP Upkeep
## Introduction
This repository contains all the files related to the SE Project:
> Develop an application for the planning of maintenance actions in a manifacturing environment

The project was realized by the Team 9:
* Marzolo Alice- 0622701587 - a.marzolo@studenti.unisa.it
* Oro Carmine- 0622701529 - c.oro@studenti.unisa.it
* Pagliara Lorenzo- 0622701576 - l.pagliara5@studenti.unisa.it
* Policastro Pasquale- 0622701477 - p.policastro6@studenti.unisa.it

## Repository structure
The repository is divided in 5 different folders, namely:
* _Frontend_: contains all the frontend code (HTML, CSS, JS). It also contains some PHP code and so in order to test the code a web server with PHP support must be used;
* _Backend_: contains all the backend code of the application. The code is written in PHP. For the deployment of the server, we used XAMPP.
* _Artifacts_: contains all the documents produced during the development. 
* _Testing_: contains all the code related to the testing. it contains 2 sub-folders, one related to the API Testing and another one related to the Unit Test.
* _Database code_: contains all the _SQL_ code used to create/populate the database.

### Frontend
The "Frontend" folder contains all the HTML/PHP templates of the project and a sub-folder "_Static_" which is divided in other 3 sub-folders:
* _js_: contains all the .js files of the project;
* _css_: contains all the .css files of the project;
* _imgs_: contains all the images of the application.

### Backend
The _"Backend"_ folders contains all the code related to server-side of our application. The routing mechanism makes use of the _.htaccess_ file in order to rewrite the Apache's Engine to route all the request made to server to the file _routes.php_.
In the same folder there also a directory, _"UnitTest"_, that contains all the test files (this same folder is also present inside the "_Testing_" one).

### Artifacts
There are the common SCRUM artifacts: user stories file, product backlog and two sprint backlog. It contains also: 
* a file, "_Architecture.pdf_", that is a short document describing the software architecture; 
* a EA document "_Design.eapx_" in which there is the global architecture model, the server architecture model, the database model and the server execution model;
* "_Final Release.pdf_", a presentation of the project, containing both the process and the product. 
* "_TestCase.pdf_", a file that documents some of the test made.
* "_SecondDelivery.pdf_", a file that describes the first sprint.
* "_ThirdDelivery.pdf_", a file that describes the second sprint.
* "_FinalRelease.pdf_", a presentation of the project describing both the product and the process.
* There is also a folder, _"Videos"_, containing some video that shows how the application works.

### Testing
The _"Testing"_ folder contains the code related to the testing carried out on our application. In this folder, there are 2 sub-folders:
* _APITesting_: contains all the .php files of the API testing and a .pdf file needed for the testing of a specific service;
* _UnitTest_: contains all the .php files of the Unit Test;

### Database code
In this folder there is all the _SQL_ code used to create/populate the database. The scripts should be executed in this specific order:
1. _role.sql_
2. _db\_creation.sql_
3. _db\_population.sql_

For the unit test we used another database, filled with specific data in order to make the tests repeatable. In order to create the database for the unit test, the scripts should be executed in this specific order:
1. _role.sql_
2. _db\_creation\_unit\_test.sql_
3. _db\_population\_unit\_test.sql_

### Useful links
| Site | URL |
| ------ | ------ |
| Trello | https://trello.com/b/j8ZlRytp/team-9 |
| Drive | https://drive.google.com/drive/folders/1meds-hs11mE7j1VS2E64lhjuqO829hvF?usp=sharing |