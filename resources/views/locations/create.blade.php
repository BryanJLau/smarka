<html>
<title>Create a Location</title>
<p>Create a Location!</p>
<form action="/locations" method="POST">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
    Text: <textarea rows="10" cols="80" name="location"
        placeholder="10 AM: 9999 Streetname Ave, Cityname, CA Zipcode"
        ></textarea><br>
    <input type="submit" value="Submit">
<form>
</html>
