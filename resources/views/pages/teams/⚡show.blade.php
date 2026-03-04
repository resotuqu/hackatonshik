<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts::app', ['title' => 'Команда'])]
class extends Component {
    public \App\Models\Team $team;

    public function mount(\App\Models\Team $team): void
    {
        $this->team = $team;
    }

    public function appendToRole($id) {
        $role = \App\Models\TeamRole::find($id);
        $role->user_id = Auth::user()->id;
        $role->save();
    }

    public function truncateFromRole($id)
    {
        $role = \App\Models\TeamRole::find($id);
        if($role->user_id == Auth::user()->id) {
            $role->user_id = null;
        }
        $role->save();
    }
};
?>

<div>
        <div class="flex flex-row gap-2">
            <x-mary-card class="card card-border h-fit w-full">
                <img src="/uploads/{{$team->image_url}}" alt="">
            </x-mary-card>
            <x-mary-card class="card card-border card-body w-full max-h-full overflow-y-auto">
                {{--Title--}}
                <h3 class="text-3xl card-title">{{$team->title}}</h3>

                {{--Description--}}
                <x-mary-card class="card card-border bg-base-300 mt-2">
                    <p class="card-title">Описание</p>
                    <x-markdown>{{$team->description}}</x-markdown>
                </x-mary-card>

                {{--Hackaton--}}
                <x-mary-card class="card card-border bg-base-300 mt-2">
                    <p class="card-title">Хакатон</p>
                    <p>{{$team->hackaton->title}}</p>
                </x-mary-card>

                {{--Author--}}
                <x-mary-card class="card card-border bg-base-300 mt-2">
                    <p class="card-title">Авторство</p>
                    <p>Создатель: {{$team->user->nickname}}</p>
                </x-mary-card>

                {{--SocialLinks--}}
                <x-mary-card class="card card-border bg-base-300 mt-2">
                    <p class="card-title">Социальные ссылки</p>
                    <div class="space-y-2">
                        @foreach($team->socialLinks as $social)
                            <x-mary-card>
                                <a href="{{$social->url}}" target="_blank" class="btn btn-primary">{{$social->name}}</a>
                            </x-mary-card>
                        @endforeach
                    </div>
                </x-mary-card>

                {{--Roles--}}
                <x-mary-card class="card card-border bg-base-300 mt-2">
                    <div class="space-y-4">
                        <p class="card-title">Роли</p>

                        @foreach($team->roles as $role)
                            <x-mary-card class="card card-border">
                                <h5 class="card-title">{{$role->title}}
                                    <div class="badge badge-primary">
                                        @if($role->user_id == null)
                                            Свободна

                                        @else
                                            Занята
                                        @endif
                                    </div>
                                </h5>
                                <x-mary-card class="card mt-2">
                                    <p class="card-title">Описание</p>
                                    <x-mary-card class="card card-border">
                                        <x-markdown>{{$role->description}}</x-markdown>
                                    </x-mary-card>
                                </x-mary-card>

                                <x-mary-card class="card">
                                    <p class="card-title">Навыки</p>
                                    <x-mary-card class="card card-border">
                                        @foreach($role->skills as $skill)
                                            <x-marybadge value="{{$skill->name}}" />
                                        @endforeach
                                    </x-mary-card>
                                </x-mary-card>



                                @if($role->user_id == null)
                                    <x-mary-button wire:click="appendToRole({{$role->id}})" class="btn-primary" label="Занять роль"/>
                                @elseif($role->user_id == Auth::user()->id)
                                    <x-mary-button wire:click="truncateFromRole({{$role->id}})" class="btn-secondary" label="Уйти с роли"/>
                                @endif


                            </x-mary-card>
                        @endforeach


                    </div>
                </x-mary-card>

            </x-mary-card>
        </div>

</div>
