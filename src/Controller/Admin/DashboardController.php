<?php 

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Entity\Disponibilite;
use App\Entity\Medecin;
use App\Entity\Patient;
use App\Entity\RendezVous;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(PatientCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Maison Médicale Santé Ensemble - Admin');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToUrl('Retourner vers le site', 'fa fa-home', '/'),
            MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home', '/'),
            MenuItem::linkToCrud('Patients', 'fa fa-user', Patient::class),
            MenuItem::linkToCrud('Médecins', 'fa fa-user-md', Medecin::class),
            MenuItem::linkToCrud('Rendez-vous', 'fa fa-calendar-check', RendezVous::class),
            MenuItem::linkToCrud('Disponibilités', 'fa fa-clock', Disponibilite::class), // ✅ ajout ici
            MenuItem::linkToCrud('Contact', 'fas fa-envelope', Contact::class),
        ];
    }
}
