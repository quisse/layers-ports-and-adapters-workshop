<?php

declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use MeetupOrganizing\Domain\ListMeetupsRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewareInterface;

final class ListMeetupsController implements MiddlewareInterface
{
    private ListMeetupsRepositoryInterface $listMeetupsRepository;
    
    private TemplateRendererInterface $renderer;
    
    public function __construct(
        ListMeetupsRepositoryInterface $listMeetupsRepository,
        TemplateRendererInterface $renderer
    ) {
        $this->listMeetupsRepository = $listMeetupsRepository;
        $this->renderer = $renderer;
    }
    
    public function __invoke(Request $request, Response $response, callable $out = null): ResponseInterface
    {
        $response->getBody()
            ->write(
                $this->renderer->render(
                    'list-meetups.html.twig',
                    [
                        'upcomingMeetups' => $this->listMeetupsRepository->listUpcomingMeetups(),
                        'pastMeetups' => $this->listMeetupsRepository->listPastMeetups(),
                    ]
                )
            );
        
        return $response;
    }
}
