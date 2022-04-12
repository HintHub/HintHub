# HintHub Technical Documentation
The framework provides a MVC structure, which allows us to reuse components and wiring additional Bundles ("Plugins").
The Doctrine ORM provided capabilities of automatically running migrations, without writing a single line of code for them. However due to the framework`s possibilities and restriction.

## Project Directory Hierachy 
We are using the following architecture as shown in this repository (only the important ones are listed):
- assets (all assets)
- templates (all templates)
- public (all public files, served by nginx)
- migrations (all migrations)
- config (all Configs)
- src/ (Source Files)
  - Entity (all Entities)
  - Model (all Models)
  - Service (all Services)
  - Controller (all Controllers)
  - Events (EventListeners for EasyAdmin and Doctrine)
  - Filter (used for the EasyAdmin dataTable View)


## Our Setup
Generally we use two docker-compose setups, which are not public yet. The first setup is the production setup, the other setup is the development setup, however this is usually called a CD/CI pipeline.

## Usage of EasyAdmin 
We are using EasyAdmin due to it's usability.
### additional extensions of EasyAdmin
We extended a few views and modified the extended Code, sometimes heavily. For example one of our requirements included a Textbox and Comment Listing, so we included that in our Code.
You can find examples for these modifications here:
- controller: https://github.com/HintHub/HintHub/blob/main/src/Controller/Admin/KommentarCrudController.php 
- views: https://github.com/HintHub/HintHub/blob/main/templates/bundles/EasyAdminBundle/crud/FehlerCrudDetail.html.twig

## Database Usage and Doctrine 2
We use Doctrine2 as ORM and MariaDB as mysql-like DBMS. This providedes us with a very strong foundation without reinventing the wheel and focussing on the core aspects of our project.

### Entities
Entities allowing a OOP like abstraction of domain driven Models which are used in the DB inside of ORM. 

We have the following Entities in this project:
- Benachrichtigung (Notification)
- Fehler (Mistake/Report)
- Kommentar (Comment)
- Modul (Module)
- Skript (A university material e.g. a Script of a Module)
- User

During work we created a large UML Diagram, however this is not public yet. 

## Symfony Services
We use Service Wrappers for a lot of functionalities, especially regarding posting comments, profile editing etc. 
Additional configuration included making the services lazy loaded - it improves reloadability,  improves resource usage and it removes possible collision conflicts e.g. when a service was already loaded in another service.
- See here: https://github.com/HintHub/HintHub/blob/main/config/services.yaml 

## Test and Dump Data (using Appfixture Bundle)
We used a PDF parser to extract the modules of the Informatics course modules and reshaped the document to a easy readable text file shown here: https://github.com/HintHub/HintHub/blob/main/src/DataFixtures/testData/testmodule_skript.txt
The worst file format CSV helped us in this case ;-) The file is loaded into AppFixtures.php and generates out of it a lot of test data, so called dump data. Which being used during deployment and testing.