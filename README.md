# Good Gateway Football

<a href="http://ggf.demo.php.nixdev.co" target="_blank">Demo</a>

## Setup development environment with Homestead (per-project installation)

1. Clone project

2. Run the following command to prepare project:
	
    ```
    EMBER_ENV=development bin/setup.sh
	```
	
3. Setup homestead/vagrant environment:
	
    ```
    ./vendor/bin/homestead make
	```

	> Remove the following lines from Homestead.yaml if you don't have this SSH keys on your machine (http://laravel.com/docs/5.0/homestead#installation-and-setup):
	> 
        authorize: ~/.ssh/id_rsa.pub
        keys:
            - ~/.ssh/id_rsa
	    

4. Run vagrant
	
    ```
    vagrant up
    ```
    
5. Add facebook settings to .env:
	

        FACEBOOK_APP_ID=1
        FACEBOOK_APP_SECRET=1
        FACEBOOK_REDIRECT_URI=http://192.168.10.10/


6. Finally, browse [http://192.168.10.10](http://192.168.10.10), you should see the main page of application.


## Testing

Add new database to the particular list in Homestead.yaml:
```
databases:
    - homestead
    - homestead_test
```
Reload vagrant and run `phpunit`:
```
vendor/bin/phpunit
```