@extends("layouts.backend")
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tile row">
                <div class="col-md-3">
                    <div id="external-events">
                        <h4 class="mb-4">Draggable Events</h4>
                        <div class="fc-event">My Event 1</div>
                        <div class="fc-event">My Event 2</div>
                        <div class="fc-event">My Event 3</div>
                        <div class="fc-event">My Event 4</div>
                        <div class="fc-event">My Event 5</div>
                        <p class="animated-checkbox mt-20">
                            <label>
                                <input id="drop-remove" type="checkbox"><span class="label-text">Remove after drop</span>
                            </label>
                        </p>
                    </div>
                </div>
                <div class="col-md-9">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("js")
    <script src="{{asset('js/backend/includes/calendar.js')}}"></script>
@endpush





