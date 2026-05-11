@section('title', 'Оценивание решений')

<div class="py-6">
    <livewire:judge.submission-list :hackaton="$hackaton" :case="$case" />
</div>

