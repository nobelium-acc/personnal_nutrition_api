{{-- @extends('layouts.mail')
@section('content')

<div class="email-section">

    <h1 style="text-align: center">Vous avez reçu un nouveau message</h1>
    <div class="sender-infos" style="margin-bottom: 15px; text-align: justify;">
        <p><b>Nom et prénoms : </b>{{$data['fullname']}}</p>
        <p><b>Email: </b>{{$data['email']}}</p>
        <p><b>Téléphone :</b>{{$data['phone']}}</p>
        <p><b>Objet :</b>{{$data['subject']}}</p>
        <div class="message-content">
            <p style="font-size: 15px; text-align: justify"> <b style="text-align: center">Message :
                </b><br>{{$data["message"]}}</p>
        </div>
    </div>

</div>

@endsection --}}


<div class="email-section">

    <h1 style="text-align: center"></h1>
    <div class="sender-infos" style="margin-bottom: 15px; text-align: justify;">
        <p><b>Personnal Nutrition : </b></p>
        <div class="message-content">
            <p style="font-size: 15px; text-align: justify">Votre code de reinitialisation est : {{ $data['code'] }}</p>
            <p style="font-size: 15px; text-align: justify">Ce code expire après 1H</p>
        </div>
    </div>

</div>