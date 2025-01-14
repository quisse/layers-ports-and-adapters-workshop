<?php

declare(strict_types=1);

namespace MeetupOrganizing\Infrastructure;

use Assert\Assert;
use Exception;
use MeetupOrganizing\Domain\ScheduledDate;
use MeetupOrganizing\Application\ScheduleMeetup;
use MeetupOrganizing\Application\MeetupScheduler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

final class ScheduleMeetupController
{
    private Session $session;
    
    private TemplateRendererInterface $renderer;
    
    private RouterInterface $router;
    
    private MeetupScheduler $meetupScheduler;
    
    public function __construct(
        Session $session,
        TemplateRendererInterface $renderer,
        RouterInterface $router,
        MeetupScheduler $meetupScheduler
    ) {
        $this->session = $session;
        $this->renderer = $renderer;
        $this->router = $router;
        $this->meetupScheduler = $meetupScheduler;
    }
    
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $formErrors = [];
        $formData = [
            // This is a nice place to set some defaults
            'scheduleForTime' => '20:00',
        ];
        
        if ($request->getMethod() === 'POST') {
            $formData = $request->getParsedBody();
            Assert::that($formData)
                ->isArray();
            
            if (empty($formData['name'])) {
                $formErrors['name'][] = 'Provide a name';
            }
            if (empty($formData['description'])) {
                $formErrors['description'][] = 'Provide a description';
            }
            try {
                ScheduledDate::fromString(
                    $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime']
                );
            } catch (Exception $exception) {
                $formErrors['scheduleFor'][] = 'Invalid date/time';
            }
            
            if (empty($formErrors)) {
                $meetupId = $this->meetupScheduler->schedule(
                    new ScheduleMeetup(
                        $this->session->getLoggedInUser()
                            ->userId()
                            ->asInt(),
                        $formData['name'],
                        $formData['description'],
                        $formData['scheduleForDate'] . ' ' . $formData['scheduleForTime']
                    )
                );
                
                $this->session->addSuccessFlash('Your meetup was scheduled successfully');
                
                return new RedirectResponse(
                    $this->router->generateUri(
                        'meetup_details',
                        [
                            'id' => $meetupId,
                        ]
                    )
                );
            }
        }
        
        $response->getBody()
            ->write(
                $this->renderer->render('schedule-meetup.html.twig', [
                    'formData' => $formData,
                    'formErrors' => $formErrors,
                ])
            );
        
        return $response;
    }
}
