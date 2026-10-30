@include('shared.resource-index', [
    'resourceTitle' => 'Event categories',
    'resourceRoute' => 'event-categories',
    'resourceItems' => $categories,
    'createRoute' => 'event-categories.create',
    'statusFilter' => ['active', 'inactive'],
    'columns' => [
        ['label' => 'Category', 'key' => 'name'],
        ['label' => 'Description', 'key' => 'description'],
        ['label' => 'Sub-categories', 'key' => 'sub_categories_count'],
        ['label' => 'Status', 'key' => 'is_active', 'type' => 'boolean']
    ],
    'showRoute' => true,
    'editRoute' => true,
    'deleteRoute' => true
])