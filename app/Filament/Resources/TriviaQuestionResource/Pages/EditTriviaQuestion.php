<?php

namespace App\Filament\Resources\TriviaQuestionResource\Pages;

use App\Filament\Resources\TriviaQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTriviaQuestion extends EditRecord
{
    protected static string $resource = TriviaQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
