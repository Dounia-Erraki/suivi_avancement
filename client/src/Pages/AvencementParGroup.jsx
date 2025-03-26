import React, { useEffect, useState } from 'react';
import { useNavigate } from "react-router-dom";
import { Table, Card, Spinner, Alert, Button } from "flowbite-react";
import { HiInformationCircle } from "react-icons/hi";
import { RiFileExcel2Fill } from "react-icons/ri";
import { FaFilePdf } from "react-icons/fa6";

export default function AvencementParGroup() {
  const [excelData, setExcelData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searched, setSearched] = useState("");
  const [filteredData, setFilteredData] = useState([])
  const [error, setError] = useState(null);


  const navigate = useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const response = await fetch("http://127.0.0.1:8000/api/AvencementParGroup");

        if (!response.ok) {
          throw new Error("La réponse du réseau n'était pas correcte");
        }

        const data = await response.json();
        setExcelData(data);

      } catch (error) {
        setError('Échec de la récupération des données');
        console.error('Erreur lors de la récupération des données :', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();

  }, []);
  useEffect(() => {
    if (searched) {
        setFilteredData(
            excelData.filter((d) =>
                d.Groupe.toLowerCase().includes(searched.toLowerCase())
            )
        );
    } else {
        setFilteredData(excelData); // Reset to full data when search is cleared
    }
}, [searched, excelData]);

  if (loading) {
    return (
      <div className="flex flex-col items-center">
        <Spinner size="xl" />
        <p className="mt-4 text-gray-600">Chargement des données...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex flex-col items-center gap-4">
        <Alert color="failure" icon={HiInformationCircle}>
          <span className="font-medium mb-4">{error}</span>
        </Alert>
        <Button color="blue" onClick={() => navigate('/importData')}>
          Importer des données
        </Button>
      </div>
    );
  }

  const handleExport = () => {
    window.location.href = 'http://127.0.0.1:8000/export/avancement-groupes';
  };

  return (
    <div className="mx-auto p-4">
      <Card>
        <div className="flex items-center justify-between">
            <h2 className="text-2xl font-bold text-black mb-4">Etat d'avancement par Groupe</h2>
            <div className="flex space-x-2">
            <button className="flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                <FaFilePdf className="h-4 w-4 mr-2" />
                <span className="text-md">PDF</span>
            </button>
            <button  className="flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50" onClick={handleExport}>
                <RiFileExcel2Fill className="h-4 w-4 mr-2" />
                <span className="text-md">Excel</span>
            </button>
        </div>
        </div>
        <form>
                    <label
                        htmlFor="search"
                        class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white"
                    >
                        Search
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg
                                class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                                />
                            </svg>
                        </div>
                        <input
                            type="search"
                            id="search"
                            value={searched}
                            onChange={e=>setSearched(e.target.value)}
                            class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Recherche Par Groupe"
                            required
                        />

                    </div>
                </form>
        <div className="overflow-x-auto" style={{ direction: 'rtl' }}>
          <Table striped hoverable className="text-black text-xs" style={{ direction: 'ltr' }}>
            <Table.Head className="bg-gray-100">
              <Table.HeadCell>Niveau</Table.HeadCell>
              <Table.HeadCell>Secteur</Table.HeadCell>
              <Table.HeadCell>Code Filière</Table.HeadCell>
              <Table.HeadCell>Filière</Table.HeadCell>
              <Table.HeadCell>Type de Formation</Table.HeadCell>
              <Table.HeadCell>Créneau</Table.HeadCell>
              <Table.HeadCell>Groupe</Table.HeadCell>
              <Table.HeadCell>Effectif Groupe</Table.HeadCell>
              <Table.HeadCell>Année de Formation</Table.HeadCell>
              <Table.HeadCell>Mode</Table.HeadCell>
              <Table.HeadCell>MH Totale</Table.HeadCell>
              <Table.HeadCell>MH Totale Réalisée</Table.HeadCell>
              <Table.HeadCell>% de Réalisation</Table.HeadCell>
              <Table.HeadCell>Ecart</Table.HeadCell>
              <Table.HeadCell>Ecart en %</Table.HeadCell>
            </Table.Head>

            <Table.Body className="divide-y text-center">
              {filteredData.map((row, index) => (
                <Table.Row key={index} className="bg-white">
                  <Table.Cell>{row.Niveau}</Table.Cell>
                  <Table.Cell>{row.Secteur}</Table.Cell>
                  <Table.Cell>{row.Code_Filiere}</Table.Cell>
                  <Table.Cell>{row.Filiere}</Table.Cell>
                  <Table.Cell>{row.Type_Formation}</Table.Cell>
                  <Table.Cell>{row.Creneau}</Table.Cell>
                  <Table.Cell className='text-left'>{row.Groupe}</Table.Cell>
                  <Table.Cell>{row.Effectif_Groupe}</Table.Cell>
                  <Table.Cell>{row.Annee_Formation}</Table.Cell>
                  <Table.Cell>{row.Mode}</Table.Cell>
                  <Table.Cell className='font-medium'>{row.MH_Totale}</Table.Cell>
                  <Table.Cell>{row.MH_Totale_Realisee}</Table.Cell>
                  <Table.Cell>
                    <span className={`font-bold ${row.Pourcentage_Realisation >= 80 ? 'text-green-600' : row.Pourcentage_Realisation >= 60 ? 'text-yellow-600' : 'text-red-600'}`}>
                      {row.Pourcentage_Realisation}%
                    </span>
                  </Table.Cell>
                  <Table.Cell className='font-medium'>{row.Ecart}</Table.Cell>
                  <Table.Cell>
                    <span className={`font-bold ${row.Ecart_Pourcentage >= 80 ? 'text-green-600' : row.Ecart_Pourcentage >= 60 ? 'text-yellow-600' : 'text-red-600'}`}>
                      {row.Ecart_Pourcentage}%
                    </span>
                  </Table.Cell>
                </Table.Row>
              ))}
            </Table.Body>
          </Table>
        </div>
      </Card>
    </div>
  );
}
