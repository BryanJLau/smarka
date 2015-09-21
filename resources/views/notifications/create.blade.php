<html>
<title>Create a Notification</title>
<p>Create a notification!</p>
<form action="/notifications" method="POST">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
    Text: <textarea rows="10" cols="80" name="text"></textarea><br>
    <input type="submit" value="Submit">
<form>
</html>
