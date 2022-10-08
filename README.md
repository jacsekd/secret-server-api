# secret-server-api
This is an implementation written in PHP of the [secret server task](https://github.com/ngabesz-wse/secret-server-task). The application uses MySQL database.

## Installation
To run the application on your local machine, you'll need an HTTP Server, PHP and MySQL. I recommend downloading XAMPP, that's what I used too.
First, you'll need to create the database. Connect to MySQL and create a database called **secret**. Connected to the database, create the table and primary key. These can be found in ```secret.sql```.
Next thing you'll need to do, is dump all the files from this repository - except ```secret.sql``` - into your HTTP server's HTML folder. If you are using XAMPP, it's called *htdocs*.
You need to check ```db/Database.php``` and modify the database host, database name, username and password vairables if needed.

## API Endpoints
The API works as its defined in the ```swagger.yaml``` file in the task's git repo.