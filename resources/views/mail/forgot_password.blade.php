<h2>{{__('mail.hello2')}}</h2>
<hr/>
<p>
    {{__('mail.forgotText1', ['url' => env('APP_URL')])}}<br/>
    {{__('mail.forgotText2')}}
</p>
<p>{{ $token }}</p>