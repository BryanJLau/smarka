# Smarka Kitchen [(Hom's Kitchen)](http://homskitchen.mezzacorp.com) Web Server #
This open source [Laravel](http://laravel.com/) web server is a basic server 
for small, informal order placements.

The original concept was the ability to place orders for food, which would 
be available for pickup. With no payment gateway, this is truly only for 
keeping track of and placing orders and a list of items (with the ability for 
a rotating inventory). The server also has a customizable downtime so that new 
orders cannot be placed and the inventory is taken down to allow the admin 
to change the inventory and process orders.

The server is built on the LAMP stack, with Laravel being the backend framework 
and Twitter Bootstrap and Angular.js on the front-end.

## Design Considerations ##
Because this is a small app written as a hobby, there are some limitations 
to the code. 
* A mail server is required. Upon placing an order, an email is sent 
to both the user (if their email is provided), as well as to the admin's 
mailbox. Please see below for configuration details.
* Security is attempted, but is not guaranteed for admin authentication. There 
is only one admin user, whose credentials are the same as the mail server. 
Because these credentials are compared against environment variables, so no 
SQL attacks can be performed.
* No sensitive data is kept (aside from phone numbers and a name).

This server was meant for non-tech-savvy people, so the admin panel is made 
as simple as possible, at the cost of some extensibility.

Uploaded files will be located in the ``` /public/uploads ``` folder, and can 
be accessed in the url as ``` http://www.example.com/uploads ```. The OpenShift 
hook has already been configured to link the ``` $OPENSHIFT_DATA_DIR ``` 
environment variable to that folder.

## Deployment ##
For local deployment, copying the .env.example file into a .env file and 
modifying it is acceptable. The MAIL\_USERNAME and MAIL\_PASSWORD will double as 
your mail server credentials, as well as your administrator credentials.

For deployment on OpenShift, you can edit the ``` /.openshift/.env ``` file. 
If pushing to a public repository such as on Github, the mail credentials must 
not be set in the .env file, but rather in the environment variables. You can 
do so using the rhc tools with the command:

```
$ rhc env set MAIL_<key>=<value> -a <app-name>
```

The keys are contained in the .env.example with the prefix ``` MAIL_ ```.

# Administrator API #
The adminstrator panel utilizes Angular.js HTTP calls to service requests. 
However, you can use an external tool such as Postman to perform calls as well, 
should you choose not to use the web GUI.

All API calls will return a status code 401 if the session is not set.

## Administrator Login ##
``` POST /admin/login ```

Parameters:

* ``` email ``` : The full email address for the mail server
* ``` password ``` : The password for the email address

Upon success, it normally redirects to the admin page, with the session 
variables set. Upon failure, it redirects back to the login page.

## Items ##
### Get item list ###
``` GET /items ```

This call does not require administrator authentication.

Parameters:

* ``` all ``` : (OPTIONAL) True to return all items (requires admin session)

Returns:

* ``` JSONArray ``` : Contains all the details pertaining to the active items 
(or all items if the all parameter and session authentication passes)

### Create item ###
``` POST /items ```

Parameters:

* ``` name ``` : The item's name
* ``` description ``` : The item's description
* ``` price ``` : The item's price (decimal, eg. 2.50, max 999.99)
* ``` active ``` : Whether or not the item is put on the active selling list 
(true or false)
* ``` picture1 ``` : The item's main picture, .jpg files only under 2 MB
* ``` picture2 ``` : (OPTIONAL) The item's secondary picture, .jpg files only 
under 2 MB

Returns:

* ``` statusCode 201 ``` on success
* ``` statusCode 400 ``` on missing parameters

### Update item details ###
``` PUT/PATCH /items/{id} ```

Parameters:

* ``` name ``` : The item's name
* ``` description ``` : The item's description
* ``` price ``` : The item's price (decimal, eg. 2.50, max 999.99)
* ``` active ``` : Whether or not the item is put on the active selling list 
(true or false)
* ``` picture1 ``` : The item's main picture, .jpg files only under 2 MB
* ``` picture2 ``` : The item's secondary picture, .jpg files only under 2 MB

Returns:

* ``` statusCode 205 ``` on success

All fields are optional; missing or blank fields will be considered unmodified.

### Update item pictures ###
```POST /items/changePictures ```

Parameters:

* ``` id ``` : The item's id
* ``` dp2 ``` : Whether or not to delete the secondary picture (true or false)
* ``` picture1 ``` : The item's main picture, .jpg files only under 2 MB
* ``` picture2 ``` : The item's secondary picture, .jpg files only under 2 MB

Returns:

* ``` statusCode 205 ``` on success

Blank picture fields will be considered unmodified.

### Delete item ###
```DELETE /items/{id} ```

Returns:

* ``` statusCode 205 ``` on success

## Locations ##
### Get locations list ###
``` GET /locations ```

This call does not require administrator authentication.

Returns:

* ``` JSONArray ``` : Contains all the locations for pickup with properties of 
``` id ``` (for testing purposes) and ``` location ``` for the location text.


### Create location ###
``` POST /locations ```

Parameters:

* ``` location ``` : The time and place for pickup

Returns:

* ``` statusCode 201 ``` on success
* ``` statusCode 400 ``` on missing parameters

### Delete location ###
```DELETE /locations/{id} ```

Returns:

* ``` statusCode 205 ``` on success

## Notifications ##
### Get latest notification ###
``` GET /notifications ```

Returns:

* ``` id ``` : ID of the notification (for testing purposes)
* ``` text ``` : Latest notification (or nothing if no notifications were set)

### Create location ###
``` POST /notifications ```

Parameters:

* ``` text ``` : The notification text

Returns:

* ``` statusCode 201 ``` on success
* ``` statusCode 400 ``` on missing parameters

## Orders ##
### Get orders list ###
``` GET /orders ```

Parameters:

* ``` all ``` : (OPTIONAL) True to return all orders (may be slow)

Returns:

* ``` orders ``` : (JSONArray) Contains all the details pertaining to the 
unpaid orders (or all orders if the all parameter and session authentication 
passes)
* ``` requiredItems ``` : (JSONArray) A summary of the total amount of items 
needed to be produced/prepared from the unpaid orders.

### Create order ###
``` POST /orders ```

This call does not require administrator authentication.

Parameters:

* ``` name ``` : The shopper's name
* ``` phone ``` : The shopper's phone number
* ``` notes ``` : Special notes to seller
* ``` location ``` : The pickup location (not a foreign key because locations 
can be deleted, and we want to preserve the data in the orders table)
* ``` email ``` : (OPTIONAL) The shopper's email address (will receive a 
confirmation email after successful placement of order)
* ``` item_array ``` : (JSONArray) A string representing the quantity of each 
ordered item in the format ``` {"qty": x, "name": y} ```, where x is a number 
and y is a string (with quotes because of JSON object notation)

Returns:

* ``` String ``` "Thank you for your order!" on success
* ``` statusCode 400 ``` on missing parameters

### Toggle paid status of an order ###
``` PUT/PATCH /orders/{id} ```

Returns:

* ``` statusCode 205 ``` on success

### Pay all upaid orders ###
```POST /orders/all ```

Returns:

* ``` statusCode 205 ``` on success
