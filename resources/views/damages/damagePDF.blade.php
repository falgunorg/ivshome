<!doctype html>
<html>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <head>
        <meta charset="utf-8">
        <title>Damage Invoice</title>
    </head>

    <style>
        #table-data {
            border-collapse: collapse;
            padding: 3px;
        }

        #table-data td, #table-data th {
            border: 1px solid black;
        }
    </style>

    <body>
        <div class="invoice-box">

            <table border="0" id="table-data" width="100%">
                <tr>
                    <td width="70px"><b>Invoice</b></td>
                    <td>: ##{{ $damage->id }}</td>
                    <td width="30px"><b>Created</b></td>
                    <td>: {{ $damage->date }}</td>
                </tr>

                <tr>
                    <td><b>User</b></td>
                    <td>: {{ $damage->user->name }}</td>
                    <td><b>Remarks</b></td>
                    <td>: {{ $damage->remarks ?? '-' }}</td>
                </tr>

                <tr>
                    <td><b>Item</b></td>
                    <td>: {{ $damage->item->name }}</td>
                    <td><b>Quantity</b></td>
                    <td>: {{ $damage->qty }}</td>
                </tr>
            </table>

            <table border="0" width="80%">
                <tr align="right">
                    <td>Best Regard</td>
                </tr>
            </table>

            <table border="0" width="80%">
                <tr align="right">
                    <td>I M S</td>
                </tr>
            </table>

        </div>
    </body>
</html>
