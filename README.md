# Union-Rich-Tech Test Project
## Author Eric Xu

## Increase/Decrease Shopper Limit
Page: My Stores > View Locations

There is "Edit" button on the each table row.
The employee will bring to the edit location page when clicking the button.
He can update the shopper limit on the page

## Shopper Check-in process
Page: My Stores > View Locations

There is Check-in button on the each row.
The button will open the check-in page.
It is a public page and any users can see it. 
So the new shoppers will see the page when entering the store location.
They will enter first name, last name and email address and click "Enter Store" button.
If the current active shoppers is smaller than the location's shopper limit, the shopper will be active.
If not, the shopper's status will be pending.
The page shows the lastly entered 3 active shoppers so that the shoppers can know if they are "active".
The page is updated automatically per 10 seconds. You can set the interval in the public.blade.php file.

## Shopper Check-out process
Page: My Stores > View Locations

Click "Queue" button on the row to manage the shoppers and check-out process.

There are filters by status, name and email.
The employee can search any shopper using the filter.
There is Check-out button for "Active" shoppers. If clicking the button, the shopper is marked as "Completed".
This page is updated automatically by ajax per 10 seconds(it can change in frontend).
There is a timer to display when the data was updated.

## Auto Check-out
There is "CheckActiveShoppers" command to update the active shoppers for more than 2hrs.
I created the task for this.
You can run this task using the below command.
php artisan schedule:work

Or you can add "php artisan schedule:run" to cron job.
