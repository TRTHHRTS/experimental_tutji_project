<h2>{{$title}}</h2>
<hr/>
@if (isset($content['error']) && $content['error'])
    <p>{{$content['message']}}</p>
@else
    <h3>Данные платежа:</h3>
    @if ($content['test'])
        <p><strong>ТЕСТОВЫЙ ПЛАТЕЖ!</strong></p>
    @endif
    <p>!!! ID заказа: {{$content['order_id']}} !!!</p>
    <p>{{$content['message']}}</p>
    <p>ID пользователя: {{$content['user_id']}}</p>
    <p>Имя пользователя: {{$content['user_name']}}</p>
    <p>Email пользователя: {{$content['user_email']}}</p>
@endif