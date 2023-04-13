# Symfony Vue Task

This task involves using Symfony as the backend and Vue as the frontend to fetch and display data about fruits from the API located at https://fruityvice.com/api/fruit/all. The fetched data is then stored in a database and displayed on the frontend.

Instructions

1. Cloning project and installing dependencies

	```git clone  git@github.com:ghulammurtaza/symfony-vue.git```

	```cd symfony-vue```        

	```composer install```

	```yarn install```

2. Setting credentials, Copy the `.env.example` file to `.env` and update the database credentials.
     
3. Create the Database
	```php bin/console doctrine:database:create```

4. Run migrations:
	```php bin/console doctrine:migrations:migrate```

        
5. Update the `MAILER_DSN` variable in the `.env` file with your gmail credentials
	
	```MAILER_DSN=gmail://yourusername@gmail.com:yourgmailpassowrd@default```

6. Fetch fruits from command

	```php bin/console fruits:fetch```
	
7. Start Servers

	```symfony server:start```
	
	```yarn encore dev-server```
	
	```messenger:consume```
	
 8. open your browser `http://127.0.0.1:8000/` to access the application.

