# CS6843_Database_restaurant_booking

I use MAMP to install a local server environment.

There are only three pages. The homepage.php is the first page.
I do searching and submitting order in separate two steps, so we have searchresult.php and processorder.php, 
customername.php and rescapacity.php are only used to interact with front end. (I used AJAX)
When you click "Search For Seats" button in the first page, the form will be posted to searchresult.php.
And then you click "Submit Request" button in searchresult.php, another form will be posted to processorder.php.
And if you click "Go to HomePage" button in searchresult.php or processorder.php, the page will go back to homepage.php.

In first page, if the name is empty, the page will tell you "Name is required", and if the name you type is not in database,
the page will tell you "Invalid name". This is implemented by AJAX, after the form is submitted and before the form is posted,
this page will send a request to customername.php so that the page can know if the name is valid. 
To make sure the verification should be finished before the form is posted, 
I set the attribute async to be false in Ajax request, so that the Ajax request is synchronous in this case. 
Also, booking date and time should not be null, this is implemented by js, I use html elements to store the error information. 
Keyword can be anything and can be null. Number of People can be null or an integer that is >= 0, 
the maximum is 999 because it's big enough to represent a restaurant's capacity and you don't have to worry about overflow.

If the number is too big or the keyword doesn't match any restaurants in the database, 
the page will turn to searchresult.php and tell the user "Sorry, no restaurant available.". 
Otherwise, the new page will represent a table with all the restaurants that match the query. 
The query expression is in searchresult.php. Now, the user has to choose a restaurant from the table and make sure
his personal information is correct. Since the cname is not unique, there may be several customers with the same name, 
so I define a select element to store all the tuples in customer table that hold the user's name. 
At this time, the number of seats can not be empty and I use AJAX here again to get information from rescapacity.php, 
so I can make sure that there are enough seats in that restaurant, 
if not, the form will not be posted and the page will tell you "Not enough seats!", 
also if you input nothing, the page will also handle it. 
If you are not satisfied with the result, you can also go back to homepage.php and begin a new search.

After the form in searchresult.php is posted to processorder.php, 
the third page will get the information and show the order just submitted by user, 
also the former orders submitted by this user will appear in this page. 
You can also go back to home page.
