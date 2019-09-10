# V1

### Update 17 Jan 2018

#### Production 17 Jan 2018
Update invoice table
`php artisan migrate`

Recalculate invoice total
`php artisan ultraklin-invoice:calculate-canceled-amount`

### Update 28 Oct 2018

#### Production 28 Oct 2018

Create Rating table
`php artisan migrate`
### Update 11 Des 2018
#### Production Des 2018
Create user store
`php artisan migrate`

Add Partner permission
`php artisan db:seed --class PatchSeeder__Permission_Partner`,

### Update 18 Oct 2018

#### Production 18 Oct 2018

Create levels table & remove unused table
`php artisan migrate`

Add sales level permission
`php artisan db:seed --class PatchSeeder__Permission_Sales_Level`

Add agent permission
`php artisan db:seed --class PatchSeeder__Permission_Sales`

### Update 14 Oct 2018

##### Production 15 Oct 2018

Create agent level table
`php artisan migrate`

Add Agent level permission
`php artisan db:seed --class PatchSeeder__Permission_Agent_Level`

### Update 18 Sep 2018

##### Production 22 Sep 2018

Refactory orders table

Add total & discount field to orders table
`php artisan migrate`

Fill total & discount to orders
`php artisan db:seed --class Modify__Fill_Total_And_Discount_Order_Table`

### Update 15 Sep 2018

##### Production 15 Sep 2018

Add code, referral field to users table
`php artisan migrate`

Add agent permission
`php artisan db:seed --class PatchSeeder__Permission_Agent`

### Update 8 Sep 2018

##### Production 8 Sep 2018

Add attribute field to services table
`php artisan migrate`

### Update 22 Aug 2018

##### Production 29 Aug 2018

Added one time use promotion (new user)

Add user promotion histories table
`php artisan migrate`

### Update 10 Aug 2018

##### Production 15 Aug 2018

New promotion schema
`php artisan migrate`

### Update 30 Jul 2018

##### Production 1 Aug 2018

Add customer client table
Add total field to invoice
`php artisan migrate`

Add client support
`php artisan passport:client --personal`

##### API

| URL                           | Desription                                                             |
| ----------------------------- | ---------------------------------------------------------------------- |
| /v1/client/token              | `{ "client_id": "", "client_secret": "" }`                             |
| /v1/client/customers/register | `{ "name": "", "email": "", "phone": "", "customerId", "client": "" }` |
| /v1/client/orders             | `{ "client": "", "customerId": "", "orders": [] }`                     |

### Update 30 Jul 2018

##### Production 30 Jul 2018

Add menu table
`php artisan migrate`

Seed menu permission
`php artisan db:seed --class PatchSeeder__Permission_Menu`

##### API

| URL             | Desription          |
| --------------- | ------------------- |
| /v1/menu        | query string `name` |
| /v1/menu/{name} |                     |

### Upadate 26 Jul 2018

###### Production 26 Jul 2018

Change invoice code format
`php artisan db:seed --class Modify__Change_Invoice_Code_Format`

### Update 25 Jul 2018

###### Production 25 Jul 2018

Add user report permission
`php artisan db:seed --class PatchSeeder__Permission_Report_User`

### Update 17 Jul 2018

###### Production 22 Jul 2018

Add target field to banners table.
`php artisan migrate`

### Update 8 Jul 2018

###### Production 22 Jul 2018

Add region permission
`php artisan db:seed --class PatchSeeder__Permission_Region`

### Update 6 Jul 2018

###### Production 22 Jul 2018

Add region table
`php artisan migrate`

Seed region table
`php artisan db:seed --class PatchSeeder__Region`

# Beta

### Update

`php artisan migrate`
`php artisan db:seed --class PatchSeeder__Permission`

### Update

`php artisan migrate`
`php artisan db:seed --class PatchSeeder__Setting`

### Update

`php artisan migrate`
`php artisan db:seed --class PatchSeeder__Admin`
