@props(['user'])

<span {{ $attributes->class(['inline-flex flex-wrap items-center gap-1']) }}>
    @foreach ($user->mainRoles() as $role)
        <flux:badge size="sm" :color="$role->badgeColor()">{{ $role->label() }}</flux:badge>
    @endforeach
</span>
