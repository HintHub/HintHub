<?php
namespace App\Service;

use App\Entity\Modul;
use App\Repository\ModulRepository;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Provides the Modul Service
 *
 * @author Ali Kemal Yalama (ali-kemal.yalama@iubh.de)
 * @date 18.12.2021
 */
class ModulService {

    private ModulRepository $modulRepository;
    private EntityManager $entityManager;

    public function __construct(ModulRepository $modulRepository, EntityManagerInterface $entityManager) {
        $this   ->  modulRepository = $modulRepository;
        $this   ->  entityManager   = $entityManager;
    }

    public function findById(int $id): Modul {
        return $this    ->  modulRepository ->  find    ($id);
    }

    public function findAll(): array {
        return $this    ->  modulRepository ->  findAll ();
    }

    public function save(Modul $modul): Modul {
        $this   ->  entityManager   ->  persist ($modul);
        $this   ->  entityManager   ->  flush   ();

        return $modul;
    }

    public function update(Modul $modul): Modul {
        $modul  =   $this   ->          findById    ($modul ->  getId());

        $modul  ->  setAktuellesSkript  ($modul ->  getAktuellesSkript());
        $modul  ->  setKuerzel          ($modul ->  getKuerzel());
        $modul  ->  setName             ($modul ->  getName());
        $modul  ->  setTutor            ($modul ->  getTutor());

        return $modul;
    }

    public function delete(int $id): int {
        $toDelete   =   $this           ->  findById    ($id);
        $this       ->  entityManager   ->  remove      ($toDelete);
        $this       ->  entityManager   ->  flush       ();

        return $toDelete    ->  getId   ();
    }
}