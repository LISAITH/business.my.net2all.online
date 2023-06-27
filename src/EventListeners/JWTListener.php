<?php


namespace App\EventListeners;

use App\Repository\EntreprisesRepository;
use App\Repository\ParticuliersRepository;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder as EncoderJsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTListener
{
    private UserRepository $userRepository;
    private SerializerInterface $serializer;
    private ParticuliersRepository $particuliersRepository;
    private EntreprisesRepository $entreprisesRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * AuthenticationSuccessListener constructor.
     */
    public function __construct(RequestStack $requestStack, UserRepository $userRepository, SerializerInterface $serializer, ParticuliersRepository $particuliersRepository, EntreprisesRepository $entreprisesRepository)
    {
        $this->userRepository = $userRepository;
        $this->particuliersRepository = $particuliersRepository;
        $this->entreprisesRepository = $entreprisesRepository;
        $this->serializer = $serializer;
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        try {
            $expiration = new \DateTime('+1 month');
            $expiration->setTime(2, 0, 0);

            $payload = $event->getData();
            $payload['exp'] = $expiration->getTimestamp();

            $user = $this->userRepository->findOneBy(["email"=>$event->getUser()->getUserIdentifier()]);
            $payload["id"] = $user->getId();
            $event->setData($payload);
        }catch (Exception){

        }
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        try {
            $u = $this->userRepository->findOneBy(["email" => $user->getUserIdentifier()]);

            if (!in_array($u->getType()->getId(), [1, 6])) {
                return;
            }

            $particular = $this->particuliersRepository->findOneBy(["user" => $u]);
            $enterprise = $this->entreprisesRepository->findOneBy(["user" => $u]);

            $d_user = json_decode($this->serializer->serialize($u, EncoderJsonEncoder::FORMAT));
            $d_user->type = $u->getType()->getId();

            $d_enterprise = json_decode($this->serializer->serialize($enterprise, EncoderJsonEncoder::FORMAT));
            if($enterprise!=null){
                $d_enterprise->pays = $enterprise->getPays()->getId();
            }

            $d_particular = json_decode($this->serializer->serialize($particular, EncoderJsonEncoder::FORMAT));
            if($particular!=null){
                $d_particular->pays = $particular->getPays()->getId();
            }

            $data['data'] = array(
                'user' => $d_user,
                "particulier" => $d_particular,
                "entreprise" => $d_enterprise,
            );
            $event->setData($data);
        } catch (Exception) {

        }
    }
}