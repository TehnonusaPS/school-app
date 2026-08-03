<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\School;

trait HasSchoolScope
{
    /**
     * Resolve the school_id for the current request context and user roles.
     */
    protected function resolveSchoolId(Request $request): ?int
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        if ($user->isSuperAdmin()) {
            return $request->input('school_id') ? (int) $request->input('school_id') : null;
        } elseif ($user->hasRole('admin_yayasan')) {
            $schoolId = $request->input('school_id');
            if ($schoolId) {
                $belongs = School::where('id', $schoolId)
                    ->where('foundation_id', $user->foundation_id)
                    ->exists();
                return $belongs ? (int) $schoolId : -1; // -1 means unauthorized
            }
            return null;
        }

        return (int) $user->school_id;
    }
}
