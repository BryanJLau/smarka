<html>
<title>Orders List</title>
<p>Orders</p>
<form action="/orders/all" method="POST">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
<input type="submit" value="Pay all orders">
</form>

<br><br>

<table border="1">
    <tr>
        <td>ID</td>
        <td>Name</td>
        <td>Phone</td>
        <td>Address</td>
        <td>Ordered Date</td>
        <td>Paid?</td>
        <td>Item List</td>
        <td>Toggle?</td>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>
                <a href="/order/{{{ $order->id }}}/edit">
                    {{ $order->name }}
                </a>
            </td>
            <td>{{ $order->phone }}</td>
            <td>{{ $order->address }}</td>
            <td>{{ $order->ordered_on }}</td>
            <td>{{ $order->paid }}</td>
            <td>
                @foreach($order->item_array as $item)
                    {{ $item->qty }} x {{ $item->name }}<br>
                @endforeach
            </td>
            <td>
                <form action="/orders/{{{ $order->id }}}" method="POST">
                    <!-- Token -->
                    <input type="hidden" name="_token"
                        value="{{{ csrf_token() }}}">
                    <!-- PUT -->
                    <input type="hidden" name="_method" value="PUT">
                    <input type="submit" value="Toggle">
                </form>
            </td>
        </tr>
    @endforeach
</table>

</html>
