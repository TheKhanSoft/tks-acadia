<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CampusRequest;
use App\Models\Campus;
use App\Services\CampusService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampusController extends Controller
{
    protected CampusService $campusService;
    
    /**
     * Create a new controller instance.
     *
     * @param CampusService $campusService
     */
    public function __construct(CampusService $campusService)
    {
        $this->campusService = $campusService;
        
        // You can uncomment and adjust these if you're using middleware for authorization
        // $this->middleware('permission:view-campuses')->only(['index', 'show']);
        // $this->middleware('permission:create-campuses')->only(['create', 'store']);
        // $this->middleware('permission:edit-campuses')->only(['edit', 'update', 'toggleActive']);
        // $this->middleware('permission:delete-campuses')->only(['destroy', 'restore', 'forceDelete']);
    }
    
    /**
     * Display a listing of the campuses.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $campuses = $this->campusService->getPaginatedCampuses($request->all());
        $filters = $request->only(['search', 'location', 'founded_year', 'is_active', 'with_trashed', 'only_trashed']);
        
        return view('admin.campuses.index', compact('campuses', 'filters'));
    }
    
    /**
     * Show the form for creating a new campus.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.campuses.create');
    }
    
    /**
     * Store a newly created campus in storage.
     *
     * @param CampusRequest $request
     * @return RedirectResponse
     */
    public function store(CampusRequest $request): RedirectResponse
    {
        $campus = $this->campusService->createCampus($request);
        
        return redirect()
            ->route('admin.campuses.index')
            ->with('success', "Campus '{$campus->name}' has been created successfully!");
    }
    
    /**
     * Display the specified campus.
     *
     * @param Campus $campus
     * @return View
     */
    public function show(Campus $campus): View
    {
        // Load related data
        $campus->load([
            'offices' => function ($query) {
                $query->where('is_active', true);
            }
        ]);
        
        return view('admin.campuses.show', compact('campus'));
    }
    
    /**
     * Show the form for editing the specified campus.
     *
     * @param Campus $campus
     * @return View
     */
    public function edit(Campus $campus): View
    {
        return view('admin.campuses.edit', compact('campus'));
    }
    
    /**
     * Update the specified campus in storage.
     *
     * @param CampusRequest $request
     * @param Campus $campus
     * @return RedirectResponse
     */
    public function update(CampusRequest $request, Campus $campus): RedirectResponse
    {
        $this->campusService->updateCampus($campus, $request);
        
        return redirect()
            ->route('admin.campuses.index')
            ->with('success', "Campus '{$campus->name}' has been updated successfully!");
    }
    
    /**
     * Toggle the activity status of the specified campus.
     *
     * @param Campus $campus
     * @return RedirectResponse
     */
    public function toggleActive(Campus $campus): RedirectResponse
    {
        $this->campusService->toggleActiveStatus($campus);
        $status = $campus->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Campus '{$campus->name}' has been {$status} successfully!");
    }
    
    /**
     * Remove the specified campus from storage (soft delete).
     *
     * @param Campus $campus
     * @return RedirectResponse
     */
    public function destroy(Campus $campus): RedirectResponse
    {
        $this->campusService->deleteCampus($campus);
        
        return redirect()
            ->route('admin.campuses.index')
            ->with('success', "Campus '{$campus->name}' has been archived. You can restore it later if needed.");
    }
    
    /**
     * Restore a soft-deleted campus.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function restore(int $id): RedirectResponse
    {
        $this->campusService->restoreCampus($id);
        $campus = $this->campusService->getCampus($id);
        
        return redirect()
            ->route('admin.campuses.index')
            ->with('success', "Campus '{$campus->name}' has been restored successfully!");
    }
    
    /**
     * Permanently delete the campus from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $campus = $this->campusService->getCampus($id, true);
        $campusName = $campus->name;
        
        $this->campusService->permanentlyDeleteCampus($id);
        
        return redirect()
            ->route('admin.campuses.index')
            ->with('success', "Campus '{$campusName}' has been permanently deleted!");
    }
    
    /**
     * Get campus data for dropdowns via AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForDropdown(Request $request)
    {
        $activeOnly = $request->input('active_only', true);
        $campuses = $this->campusService->getCampusesForDropdown($activeOnly);
        
        return response()->json($campuses);
    }
}