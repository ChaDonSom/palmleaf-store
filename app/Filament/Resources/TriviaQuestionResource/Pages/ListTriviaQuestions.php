<?php

namespace App\Filament\Resources\TriviaQuestionResource\Pages;

use App\Filament\Resources\TriviaQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTriviaQuestions extends ListRecords
{
    protected static string $resource = TriviaQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
