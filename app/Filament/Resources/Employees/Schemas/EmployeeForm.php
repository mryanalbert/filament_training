<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\City;
use App\Models\State;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Relationship')
                    ->schema([
                        Select::make('country_id')
                            ->relationship(
                                name: 'country',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            })
                            ->required(),
                        Select::make('state_id')
                            ->options(
                                fn(Get $get): Collection => State::query()
                                    ->where('country_id', $get('country_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('city_id', null))
                            ->required(),
                        Select::make('city_id')
                            ->options(
                                fn(Get $get): Collection => City::query()
                                    ->where('state_id', $get('state_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('department_id')
                            ->relationship(
                                name: 'department',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('User Name')
                    ->description('Put the user name details in.')
                    ->schema([
                        TextInput::make('first_name')
                            ->required(),
                        TextInput::make('last_name')
                            ->required(),
                        TextInput::make('middle_name')
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('User Address')
                    ->schema([
                        TextInput::make('address')
                            ->required(),
                        TextInput::make('zip_code')
                            ->required(),
                    ])
                    ->columns()
                    ->columnSpanFull(),

                Section::make('Dates')
                    ->schema([
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->required(),
                        DatePicker::make('date_hired')
                            ->native(false)
                            ->required()
                    ])
                    ->columns(2)
                    ->columnSpanFull()

            ]);
    }
}
