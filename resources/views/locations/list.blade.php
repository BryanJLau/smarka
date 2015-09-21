<html>
<title>Locations List</title>
<p>Locations List</p>
<a href="/locations/create">Create a new Locations</a>

<table border="1">
    <tr>
        <td>ID</td>
        <td>Location</td>
        <td>Delete?</td>
    </tr>
    @foreach($locations as $location)
        <tr>
            <td>{{ $location->id }}</td>
            <td>{{ $location->location }}</td>
            <td>
                <form action="/locations/{{{ $location->id }}}" method="POST">
                    <!-- Token -->
                    <input type="hidden" name="_token"
                        value="{{{ csrf_token() }}}">
                    <!-- DELETE -->
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="submit" value="Submit">
                </form>
            </td>
        </tr>
    @endforeach
</table>

</html>
