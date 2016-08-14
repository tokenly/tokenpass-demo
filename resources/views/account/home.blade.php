<h2>Welcome {{ $user->email }}</h2>
<p>
	This is a demo application.
</p>
<p>
	<a href="{{ env('TOKENPASS_PROVIDER_HOST') }}" target="_blank">Tokenpass Settings</a>
	|
	<a href="{{ route('account.logout') }}">Logout</a>
</p>
