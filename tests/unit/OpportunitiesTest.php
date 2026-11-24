<?php

namespace ImpulseTechnologies\Opportunities\Tests\Unit;

use Carbon\Carbon;
use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Illuminate\Support\Facades\Request;
use ImpulseTechnologies\Opportunities\Components\TenderList;
use ImpulseTechnologies\Opportunities\Components\VacancyList;
use ImpulseTechnologies\Opportunities\Components\TenderDetail;
use ImpulseTechnologies\Opportunities\Models\Tender;
use ImpulseTechnologies\Opportunities\Models\Vacancy;
use ReflectionProperty;
use System\Tests\Bootstrap\PluginTestCase;

class OpportunitiesTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-06-15 10:00:00');
        Tender::query()->delete();
        Vacancy::query()->delete();
    }

    public function tearDown(): void
    {
        Carbon::setTestNow();
        Request::replace([]);

        parent::tearDown();
    }

    protected function tender(string $title, ?string $closing, array $extra = []): Tender
    {
        return Tender::create(array_merge(['title' => $title, 'closing_at' => $closing, 'is_published' => true], $extra));
    }

    protected function listOf(string $class, array $properties = [], array $query = [])
    {
        Request::replace($query);
        $component = new $class(null, $properties + ['perPage' => 0, 'loadAssets' => false]);
        $controller = new ReflectionProperty($class, 'controller');
        $controller->setAccessible(true);
        $controller->setValue($component, new Controller(Theme::load('zica')));
        $component->prepareVars();

        return $component;
    }

    /**
     * @testdox A slug is made from the title, and status follows publishing and the closing date
     */
    public function testSlugAndStatus()
    {
        $open = $this->tender('Supply of Office Furniture', '2026-06-20 12:00:00');
        $closed = $this->tender('Audit services', '2026-06-01 12:00:00');
        $draft = $this->tender('Draft tender', '2026-07-01 12:00:00', ['is_published' => false]);
        $undated = $this->tender('Pre-qualification register', null);

        $this->assertSame('supply-of-office-furniture', $open->slug);
        $this->assertSame(['open', 'closed', 'draft', 'open'], [$open->status, $closed->status, $draft->status, $undated->status]);
        $this->assertSame(5, $open->days_left);
        $this->assertNull($closed->days_left);
    }

    /**
     * @testdox Lists show only published records; open lists soonest closing first, undated last
     */
    public function testOpenList()
    {
        $this->tender('Later', '2026-07-30 12:00:00');
        $this->tender('Sooner', '2026-06-18 12:00:00');
        $this->tender('Undated', null);
        $this->tender('Closed', '2026-05-01 12:00:00');
        $this->tender('Draft', '2026-06-20 12:00:00', ['is_published' => false]);
        $this->tender('Scheduled', '2026-06-25 12:00:00', ['published_at' => '2026-06-20 09:00:00']);

        $this->assertSame(['Sooner', 'Later', 'Undated'], $this->listOf(TenderList::class)->records->pluck('title')->all());
        $this->assertSame(['Closed'], $this->listOf(TenderList::class, ['status' => 'closed'])->records->pluck('title')->all());
        $this->assertSame(['Sooner', 'Later', 'Undated', 'Closed'], $this->listOf(TenderList::class, ['status' => 'all'])->records->pluck('title')->all());
    }

    /**
     * @testdox The query-string filter offers only choices with records and ignores unknown values
     */
    public function testFilter()
    {
        Vacancy::create(['title' => 'Accountant', 'employment_type' => 'full-time', 'closing_at' => '2026-07-01', 'is_published' => true]);
        Vacancy::create(['title' => 'Intern', 'employment_type' => 'internship', 'closing_at' => '2026-07-01', 'is_published' => true]);

        $all = $this->listOf(VacancyList::class);
        $this->assertSame(['full-time', 'internship'], array_keys($all->filters));

        $interns = $this->listOf(VacancyList::class, [], ['filter' => 'internship']);
        $this->assertSame(['Intern'], $interns->records->pluck('title')->all());

        $unknown = $this->listOf(VacancyList::class, [], ['filter' => 'volunteer']);
        $this->assertNull($unknown->filter);
        $this->assertCount(2, $unknown->records);
    }

    /**
     * @testdox The detail component finds published records by slug, closed ones included, never drafts
     */
    public function testDetail()
    {
        $this->tender('Closed tender', '2026-05-01 12:00:00');
        $this->tender('Draft tender', '2026-07-01 12:00:00', ['is_published' => false]);

        $find = fn ($slug) => (new TenderDetail(null, ['slug' => $slug]))->loadRecord();

        $this->assertSame('Closed tender', $find('closed-tender')->title);
        $this->assertNull($find('draft-tender'));
        $this->assertNull($find('missing'));
    }
}
