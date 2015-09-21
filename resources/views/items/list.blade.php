<html>
<title>Items List</title>
<p>List</p>
<a href="/items/create">Create a new item</a>

<table border="1">
    <tr>
        <td>ID</td>
        <td>Name</td>
        <td>Description</td>
        <td>Price</td>
        <td>Active</td>
        <td>Picture 1</td>
        <td>Picture 2</td>
    @foreach($items as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>
                <a href="/items/{{{ $item->id }}}/edit">
                    {{ $item->name }}
                </a>
            </td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->active }}</td>
            <td>
                <img src="/uploads/{{{ $item->hash }}}_1.jpg">
            </td>
            <td>
                @if($item->picture2)
                    <img src="/uploads/{{{ $item->hash }}}_2.jpg">
                @else
                    No second image
                @endif
            </td>
        </tr>
    @endforeach
</table>

</html>
