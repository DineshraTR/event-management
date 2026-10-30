@include('shared.resource-index', [
    'resourceTitle' => 'Services',
    'resourceRoute' => 'services',
    'resourceItems' => $services,
    'createRoute' => 'services.create',
    'statusFilter' => ['active', 'inactive'],
    'columns' => [
        ['label' => 'Service', 'key' => 'name'],
        ['label' => 'Description', 'key' => 'description'],
        ['label' => 'Cost price', 'key' => 'cost_price', 'type' => 'money'],
        ['label' => 'Status', 'key' => 'is_active', 'type' => 'boolean']
    ],
    'showRoute' => true,
    'editRoute' => true,
    'deleteRoute' => true
])