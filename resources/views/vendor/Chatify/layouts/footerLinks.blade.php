<script src="https://js.pusher.com/7.0.3/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;

    @if (!empty(config('chatify.pusher.key')))
    var pusher = new Pusher("{{ config('chatify.pusher.key') }}", {
        encrypted: true,
        cluster: "{{ config('chatify.pusher.options.cluster') }}",
        authEndpoint: '{{ Route::has('pusher.auth') ? route('pusher.auth') : (Route::has('api.pusher.auth') ? route('api.pusher.auth') : url('/chats/chat/auth')) }}',
        auth: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    });
    @else
    // Safe no-op stub when Pusher keys are not configured (polling handles updates)
    var pusher = {
        subscribe: function () { return { bind: function () {} }; },
        unsubscribe: function () {},
        connection: { bind: function () {}, state: 'connected' }
    };
    @endif

    // Bellow are all the methods/variables that using php to assign globally.
    const allowedImages = {!! json_encode(config('chatify.attachments.allowed_images')) !!} || [];
    const allowedFiles = {!! json_encode(config('chatify.attachments.allowed_files')) !!} || [];
    const getAllowedExtensions = [...allowedImages, ...allowedFiles];
    const getMaxUploadSize = {{ method_exists('Chatify', 'getMaxUploadSize') || is_callable(['Chatify', 'getMaxUploadSize']) ? Chatify::getMaxUploadSize() : 157286400 }};
</script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
