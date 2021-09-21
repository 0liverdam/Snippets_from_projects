<?php

namespace App\Http\Controllers;

use App\Category;
use App\Competence;
use App\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\SeenContent;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;


class ContentController extends Controller
{

    public function getContentPiece($contentType, $id) {

        $firstContent = Content::where('id', $id)->first();

        if (!$firstContent->guestContent and !Auth::check()) {
            Session::put('url', '/content/' . $contentType . '=' . $id);
            return redirect()->route('login');
        }

        /* set article and podcasts as viewed */
        if ($firstContent->content_type != 'Video') {
            SeenContent::setViewed($firstContent->id);
        }

        return view('pages.content', ['firstContent' => $firstContent,]);
    }

    public function getContentWithCompetence($competence, $contentType, $id) {


        $firstContent = Content::all()->where('id', $id)->first();

        //IF guest is trying open not guest content -> redirect to login
        if (!$firstContent->guestContent and !Auth::check()) {
            Session::put('url', '/competence/' . $competence . '/' . $contentType . '=' . $id);
            return redirect()->route('login');
        }

        $locale = Lang::getLocale();
        $revisitContent = collect();
        if (Auth::user()) {
            $revisitContent = auth()->user()->seenContent()->
            join('contents', 'seen_contents.content_id', 'contents.id')->
            join('competence_contents', 'contents.id', 'competence_contents.content_id')->
            join('competences', 'competence_contents.competence_id', 'competences.id')->
            where('competences.slug', $competence)->
            where('locale', 'like', '%'.$locale.'%')->
            where('contents.id', '!=', $id)->
            select('content_type', 'contents.id', 'title', 'contents.priority', 'body', 'contents.thumbnail', 'duration', 'guestContent', 'excerpt', 'link')->
            orderBy('priority', 'ASC')->get();
        }

        $nextContent = Competence::all()->where('slug', $competence)->first()->contents()->
            where('locale', 'like', '%'.$locale.'%')->
            where('contents.id', '!=', $id)->
            whereNotIn('contents.id', array_column($revisitContent->toArray(), 'id'))->
            select('content_type', 'id', 'title', 'priority', 'body', 'thumbnail', 'duration', 'guestContent', 'excerpt', 'link')->
            orderBy('priority', 'ASC')->
            get();

        /* set article and podcasts as viewed */
        if ($firstContent->content_type != 'Video') {
            SeenContent::setViewed($firstContent->id);
        }

        $header = Competence::select('name', 'slug', DB::raw("'competence' as type"))->where('slug', $competence)->first();

        return view('pages.collection', [
            'firstContent' => $firstContent,
            'header' => $header, 'next' => $nextContent, 'revisit' => $revisitContent
        ]);
    }

    public function getContentWithCategory($category, $contentType, $id) {


        $firstContent = Content::all()->where('id', $id)->first();

        if (!$firstContent->guestContent and !Auth::check()) {
            Session::put('url', '/category/' . $category . '/' . $contentType . '=' . $id);
            return redirect()->route('login');
        }

        $locale = Lang::getLocale();
        $revisitContent = collect();
        if (Auth::user()) {
            $revisitContent = auth()->user()->seenContent()->
            join('contents', 'seen_contents.content_id', 'contents.id')->
            join('competence_contents', 'contents.id', 'competence_contents.content_id')->
            join('competences', 'competence_contents.competence_id', 'competences.id')->
            join('categories', 'competences.category_id', '=', 'categories.id')->
            where('categories.slug', $category)->
            where('locale', 'like', '%'.$locale.'%')->
            where('contents.id', '!=', $id)->
            select('content_type', 'contents.id', 'title', 'contents.priority', 'body', 'contents.thumbnail', 'duration', 'guestContent', 'excerpt')->
            orderBy('priority', 'ASC')->get();
        }

        $nextContent = Category::all()->where('slug', $category)->first()->competences()->
            join('competence_contents', 'competences.id', 'competence_contents.competence_id')->
            join('contents', 'competence_contents.content_id', 'contents.id')->
            where('locale', 'like', '%'.$locale.'%')->
            where('contents.id', '!=', $id)->
            whereNotIn('contents.id', array_column($revisitContent->toArray(), 'id'))->
            select('content_type', 'contents.id', 'title', 'contents.priority', 'body', 'contents.thumbnail', 'duration', 'guestContent', 'excerpt')->
            orderBy('priority', 'ASC')->
            get();

        /* set article and podcasts as viewed */
        if ($firstContent->content_type != 'Video') {
            SeenContent::setViewed($firstContent->id);
        }

        $header = Category::select('name', 'slug', DB::raw("'category' as type"))->where('slug', $category)->first();

        return view('pages.collection', [
            'firstContent' => $firstContent,
            'header' => $header, 'next' => $nextContent, 'revisit' => $revisitContent
        ]);
    }

    public function viewed(Request $request)
    {
        SeenContent::setViewed($request->content_id);
    }
}
