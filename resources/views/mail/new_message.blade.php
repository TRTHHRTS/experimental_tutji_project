<h2>{{__('mail.hello', ['name' => $username])}}</h2>
<h3>{{__('mail.newMesTitle', ['sender' => $sender])}}</h3>
<h3>Сообщение пришло {{$date}}</h3>
<h3>Текст: '{{$mes}}'</h3>