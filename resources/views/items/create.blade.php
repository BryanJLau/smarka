<html>
<title>Create an Item</title>
<p>Create an item!</p>
<form action="/items" method="POST" enctype="multipart/form-data">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
    Item Name: <input type="text" name="name"><br>
    Description: <textarea rows="10" cols="80" name="description"></textarea><br>
    Price: <input type="text" name="price" placeholder="4.99"><br>
    This week's menu? <input type="checkbox" name="active"><br>
    Picture 1: <input type="file" name="picture1"><br>
    Picture 2: <input type="file" name="picture2"><br>
    <input type="submit" value="Submit">
<form>
</html>
