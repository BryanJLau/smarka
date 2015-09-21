<html>
<title>Edit an Item - {{ $id }}</title>
<p>Edit an item! - {{ $id }}</p>
<form action="/items/{{{ $id }}}" method="POST" enctype="multipart/form-data">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
    <!-- PUT -->
    <input type="hidden" name="_method" value="PUT">
    
    Item Name: <input type="text" name="name"><br>
    Description: <textarea rows="10" cols="80" name="description"></textarea><br>
    Price: <input type="text" name="price" placeholder="4.99"><br>
    This week's menu? <input type="checkbox" name="active"><br>
    Picture 1: <input type="file" name="picture1"><br>
    Delete previous picture 2? <input type="checkbox" name="dp2"><br>
    Picture 2: <input type="file" name="picture2"><br>
    <input type="submit" value="Submit">
</form>

<br><br>

DELETE THE ITEM<br>
<form action="/items/{{{ $id }}}" method="POST">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
    <!-- PUT -->
    <input type="hidden" name="_method" value="DELETE">
    <input type="submit" value="Submit">
</form>
</html>
