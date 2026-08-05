@extends('mail.mailTemplate')
@section('content')

@if($log->assigned_for == 1)
<p class="email-text">Hello {{ $user->getGuranteedNameText() }},</p>
@endif

@if($log->assigned_for == 2)
<p class="email-text">Hello Admin,</p>
@endif

<p class="email-text mb-medium">A Device has been checked out under your name. Below are the details:</p>

<table class="info-table mb-medium">
    <tbody>
      <tr>
          <td class="label-column">
              Device Name:
          </td>
          <td class="value-column">
              {{ $device->getDeviceName() }}
          </td>
      </tr>
      <tr>
          <td class="label-column">
              Device Tag:
          </td>
          <td class="value-column">
              {{ $device->asset_tag }}
          </td>
      </tr>
      <tr>
          <td class="label-column">
              Device Serial:
          </td>
          <td class="value-column">
              {{ $device->serial }}
          </td>
      </tr>
      @if($device->model->name && $device->model->name != "")
          <tr>
              <td class="label-column">
                  Device Model:
              </td>
              <td class="value-column">
                  {{ $device->model->name . ' ' . $device->model->modelno }}
              </td>
          </tr>
      @endif
      @if($device->company->name && $device->company->name != "")
          <tr>
              <td class="label-column">
                  Device Company:
              </td>
              <td class="value-column">
                  {{ $device->company->name }}
              </td>
          </tr>
      @endif
      @if($device->model->category && $device->model->category->name != "")
          <tr>
              <td class="label-column">
                  Device Category:
              </td>
              <td class="value-column">
                  {{ $device->model->category->name }}
              </td>
          </tr>
      @endif
      @if($allocationType)
          <tr>
              <td class="label-column">
                  Allocation Type:
              </td>
              <td class="value-column">
                  {{ $allocationType }}
              </td>
          </tr>
      @endif
      @if(isset($user->username))
          <tr>
              <td class="label-column">
                  Assigned Username:
              </td>
              <td class="value-column">
                  {{ $user->username }}
              </td>
          </tr>
      @endif
      @if(isset($user->email))
          <tr>
              <td class="label-column">
                  Assigned Email:
              </td>
              <td class="value-column">
                  {{ $user->email }}
              </td>
          </tr>
      @endif
      @if($ram)
          <tr>
              <td class="label-column">
                  RAM:
              </td>
              <td class="value-column">
                  {{ $ram }} GB
              </td>
          </tr>
      @endif
      @if($hdd)
          <tr>
              <td class="label-column">
                  Storage:
              </td>
              <td class="value-column">
                  {{ $hdd }} GB
              </td>
          </tr>
      @endif
      <tr>
          <td class="label-column">
              Checkout Date:
          </td>
          <td class="value-column">
              {{ CommonHelper::displayDateTime($device->last_checkout, 'datetime', 'display') }}
          </td>
      </tr>
      @if($device->expected_checkin)
          <tr>
              <td class="label-column">
                  Expected Checkin Date:
              </td>
              <td class="value-column">
                 {{ CommonHelper::displayDateTime($device->expected_checkin, 'datetime', 'display') }}
              </td>
          </tr>
      @endif
      @if($log->note)
          <tr>
              <td class="label-column">
                  Additional Notes:
              </td>
              <td class="value-column">
                 {{ $log->note }}
              </td>
          </tr>
      @endif
    </tbody>
</table>

@if( $device->requireAcceptance() && $eula != "" )
  <p class="email-text">Please read the terms of use below, and click on the link at the bottom to confirm that you read and agree to the terms of use, and have received the device.</p>
@elseif( $device->requireAcceptance() && $eula == "" )
  <p class="email-text">Please click on the link at the bottom to confirm that you have received the device.</p>
@elseif( ! $device->requireAcceptance() && $eula != "" && $device->showEula() == 1)
  <p class="email-text">Please read the terms of use below:</p>
@endif

{{-- @if($device->showEula() == 1)
  <p class="email-text">{!! html_entity_decode($eula) !!}</p>
@endif --}}

@if($direct_accept_link)
  <p class="email-text">
    <a class="mr-large" href="{{ url('confirm/' . $log->id . '/1/' . $log->access_code) }}"><img src="{{ asset('uploads') . '/btn-accept.png'}}"/></a>
    @if($direct_accept_link_type == 2)
        <a class="ml-large" href="{{ url('confirm/' . $log->id . '/2/' . $log->access_code) }}"><img src="{{ asset('uploads') . '/btn-decline.png'}}"/></a>
    @endif
  </p>
@elseif ($device->requireAcceptance())
  <p><a href="{{ Config::get('app.url') }}device/accept_checkout/{{ $log->id }}">I agree to terms of use and have received the device.</a></p>
@endif

@if(isset($site_name))
    <p class="email-footer mt-large">
        Thank you,<br>
        @if($email_thankuby)
            {{ $email_thankuby }}<br>
        @endif
        {{ $site_name }}
    </p>
@endif
{{-- @if($log->assigned_for == 1)
  <p>Hello {{ $user->getGuranteedNameText() }},</p>
@endif
@if($log->assigned_for == 2)
  <p>Hello Admin,</p>
@endif

<p>A Device has been checked out under your name. Below are the details:</p>

<p><b>Device Name:</b> {{ $device->getDeviceName() }}</p>
<p><b>Device Tag:</b> {{ $device->asset_tag }}</p>
<p><b>Device Serial:</b> {{ $device->serial }}</p>
@if($device->model->name && $device->model->name != "")
  <p><b>Device Model:</b> {{ $device->model->name . ' ' . $device->model->modelno }}</p>
@endif

@if($device->company->name && $device->company->name != "")
  <p><b>Device Company:</b> {{ $device->company->name }}</p>
@endif

@if($device->model->category && $device->model->category->name != "")
  <p><b>Device Category:</b> {{ $device->model->category->name }}</p>
@endif

@if($allocationType)
  <p><b>Allocation Type:</b> {{ $allocationType }}</p>
@endif

@if(isset($user->username))<p><b>Assigned Username:</b> {{ $user->username }}</p>@endif
@if(isset($user->email))<p><b>Assigned Email:</b> {{ $user->email }}</p>@endif
@if($ram)
  <p><b>RAM:</b> {{ $ram }} GB</p>
@endif

@if($hdd)
  <p><b>Storage:</b> {{ $hdd }} GB</p>
@endif

<p><b>Checkout Date:</b> {{ CommonHelper::getDateAs($device->last_checkout, "d/m/Y", "Y-m-d H:i:s") }}</p>

@if($device->expected_checkin)
  <p><b>Expected Checkin Date:</b> {{ CommonHelper::getDateAs($device->expected_checkin, "d/m/Y", "Y-m-d") }}</p>
@endif

@if($log->note)
  <p><b>Additional Notes:</b> {{ $log->note }}
@endif
<br/>
@if( $device->requireAcceptance() && $eula != "" )
<p>Please read the terms of use below, and click on the link at the bottom to confirm that you read and agree to the terms of use, and have received the device.</p>
@elseif( $device->requireAcceptance() && $eula == "" )
<p>Please click on the link at the bottom to confirm that you have received the device.</p>
@elseif( ! $device->requireAcceptance() && $eula != "" && $device->showEula() == 1)
<p>Please read the terms of use below:</p>
@endif

@if($device->showEula() == 1)
<p>{!! html_entity_decode($eula) !!}</p>
@endif

@if($direct_accept_link)
  <table border="0" cellpadding="0" cellspacing="0" width="100%" >

  <p>
    <a href="{{ url('confirm/' . $log->id . '/1/' . $log->access_code) }}" style="margin-right: 30px;"><img src="{{ asset('uploads') . '/btn-accept.png'}}"/></a>
    @if($direct_accept_link_type == 2)
      <a href="{{ url('confirm/' . $log->id . '/2/' . $log->access_code) }}" style="margin-left: 30px;"><img src="{{ asset('uploads') . '/btn-decline.png'}}"/></a>
    @endif
  </p>
@elseif( $device->requireAcceptance() )
<p><a href="{{ Config::get('app.url') }}device/accept_checkout/{{ $log->id }}">I agree to terms of use and have received the device.</a></p>
  </table>
@endif

@if($site_name) 
<p>
Thank you,
<br/>
@if($email_thankuby)
{{ $email_thankuby }} <br/> 
@endif
{{ $site_name }}
</p>
@endif --}}

@endsection
