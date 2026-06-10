@foreach($properties as $property)
    <x-property-card-grid :property="$property" />
@endforeach
