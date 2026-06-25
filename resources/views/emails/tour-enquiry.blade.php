<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tour Enquiry</title>
</head>
<body>

<h2>New Tour Enquiry Received From Mahakumbh Tours & Travels Website</h2>

@if(isset($enquiry) && $enquiry)
<table cellpadding="8" cellspacing="0" border="1" width="100%">
    <tr>
        <th align="left">Name</th>
        <td>{{ $enquiry->full_name }}</td>
    </tr>

    <tr>
        <th align="left">Email</th>
        <td>{{ $enquiry->email }}</td>
    </tr>

    <tr>
        <th align="left">Phone</th>
        <td>{{ $enquiry->phone_number }}</td>
    </tr>

    <tr>
        <th align="left">Number of Travellers</th>
        <td>{{ $enquiry->number_of_travelers }}</td>
    </tr>

    <tr>
        <th align="left">Preferred Dates</th>
        <td>{{ $enquiry->preferred_dates ?: 'N/A' }}</td>
    </tr>

    <tr>
        <th align="left">Tour</th>
        <td>{{ $enquiry->tour?->title ?? 'N/A' }}</td>
    </tr>

    <tr>
        <th align="left">Vehicle Category</th>
        <td>{{ $enquiry->vehicleCategory?->category ?? 'N/A' }}</td>
    </tr>

    <tr>
        <th align="left">Special Requirements</th>
        <td>{{ $enquiry->special_requirements ?: 'N/A' }}</td>
    </tr>
</table>
@else
<p>No enquiry data available.</p>
@endif

<br>

<p>
    This enquiry was submitted from the Mahakumbh Tours website.
</p>

</body>
</html>