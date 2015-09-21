<html>
<title>Orders List</title>
<p>Orders</p>
<form action="/orders/all" method="POST">
    <!-- Token -->
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
<input type="submit" value="Pay all orders">
</form>

<a href="/orders?all=1">All orders</a>
<br>
<a href="/orders">Unpaid orders</a>

<br><br>

This week's batch:<br>
<table border="1">
    <tr>
        <td>Quantity</td>
        <td>Name</td>
    </tr>
@foreach($requiredItems as $name => $quantity)
    <tr>
        <td>{{ $quantity }}</td>
        <td>{{ $name }}</td>
    </tr>
@endforeach
</table>
<br><br>

<table border="1">
    <tr>
        <td>ID</td>
        <td>Name</td>
        <td>Phone</td>
        <td>Address</td>
        <td>Pickup</td>
        <td>Email</td>
        <td>Ordered Date</td>
        <td>Total</td>
        <td>Paid?</td>
        <td>Item List</td>
        <td>Toggle?</td>
    @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->name }}</td>
            <td>{{ $order->phone }}</td>
            <td>{{ $order->address }}</td>
            <td>{{ $order->location }}</td>
            <td>{{ $order->email }}</td>
            <td>{{ $order->ordered_on }}</td>
            <td>{{ $order->total }}</td>
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
