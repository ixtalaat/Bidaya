<h2>New message from the Bidaya website</h2>

<p><strong>Name:</strong> {{ $contactMessage->name }}</p>
<p><strong>Phone:</strong> {{ $contactMessage->phone }}</p>
@if ($contactMessage->email)
    <p><strong>Email:</strong> {{ $contactMessage->email }}</p>
@endif
@if ($contactMessage->subject)
    <p><strong>Subject:</strong> {{ $contactMessage->subject }}</p>
@endif

<p><strong>Message:</strong></p>
<p>{{ $contactMessage->message }}</p>
