<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if( \App\Models\Museum::first())
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url("museum/".\App\Models\Museum::first()->id ."/show") }}'><i class="las la-landmark"></i> Museum</a></li>
@else
    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('museum/create') }}'><i class="las la-plus"></i> Museum</a></li>
@endif
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('artwork') }}'><i class="las la-paint-brush"></i> Artworks</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('media') }}'><i class="nav-icon las la-image"></i> Media</a></li>
