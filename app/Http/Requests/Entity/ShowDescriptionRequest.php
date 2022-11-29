<?php

namespace App\Http\Requests\Entity;

use App\Contracts\ShowEntityRequestContract;
use App\Services\ContentService;
use Dogovor24\Authorization\Services\AuthAbilityService;
use Dogovor24\Authorization\Services\AuthUserService;
use Illuminate\Foundation\Http\FormRequest;

class ShowDescriptionRequest extends FormRequest implements ShowEntityRequestContract
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $contentService = new ContentService($this->route('entity')->id);
            $authService = new AuthUserService();
            $content = $contentService->getContent();
            if(
                !($content && $content->is_ready && $content->is_published)
                &&
                !($authService->checkAuth() && $contentService->getRoles($authService->getId(), true)->count())
                &&
                !($authService->checkAuth() && (new AuthAbilityService())->userHasAbility('document-entity-view'))
            )
                $validator->errors()->add('content', 'Access denied');
        });
    }
}
