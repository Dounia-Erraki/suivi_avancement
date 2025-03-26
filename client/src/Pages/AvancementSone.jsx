import { Card, Spinner, Table } from 'flowbite-react';
import React, { useEffect, useState } from 'react'
import { HiInformationCircle } from 'react-icons/hi';
import { useNavigate } from 'react-router-dom';
import { RiFileExcel2Fill } from "react-icons/ri";
import { FaFilePdf } from "react-icons/fa6";

export default function AvancementSone() {
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
            const response = await fetch("http://127.0.0.1:8000/api/avancementSone");

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
                    d['Groupe'].toLowerCase().includes(searched.toLowerCase())
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
          window.location.href = 'http://127.0.0.1:8000/export/avancement-s1';
        };
        
  return (
    <div className='mx-auto p-4'>
        <Card>
        <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold text-black mb-4">Etat d'avancement Programme de 1er Semestre</h2>
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
                        for="search"
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
                            value={searched}
                            onChange={e=>setSearched(e.target.value)}
                            id="search"
                            class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Recherche Par Groupe"
                            required
                        />

                    </div>
                </form>
        <div className='overflow-x-auto'>
            <Table striped  className='text-black text-xs'>
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
                        <Table.HeadCell>Code Module</Table.HeadCell>
                        <Table.HeadCell>Module</Table.HeadCell>
                        <Table.HeadCell>Régionale</Table.HeadCell>
                        <Table.HeadCell>MHP S1</Table.HeadCell>
                        <Table.HeadCell>MHSYN S1</Table.HeadCell>
                        <Table.HeadCell>MH Totale</Table.HeadCell>
                        <Table.HeadCell>MH Réalisée Présentiel</Table.HeadCell>
                        <Table.HeadCell>MH Réalisée Sync</Table.HeadCell>
                        <Table.HeadCell>MH Réalisée Globale</Table.HeadCell>
                </Table.Head>
                <Table.Body>
                    {
                        filteredData?.map((ele,ind)=>(
                            <tr key={ind}>
                                <Table.Cell>{ele['Niveau']}</Table.Cell>
                                <Table.Cell>{ele['Secteur']}</Table.Cell>
                                <Table.Cell>{ele['Code_Filière']}</Table.Cell>
                                <Table.Cell>{ele['Filière']}</Table.Cell>
                                <Table.Cell>{ele['Type_de_Formation']}</Table.Cell>
                                <Table.Cell>{ele['Creneau']}</Table.Cell>
                                <Table.Cell>{ele['Groupe']}</Table.Cell>
                                <Table.Cell>{ele['Effectif_Groupe']}</Table.Cell>
                                <Table.Cell>{ele['Année_de_Formation']}</Table.Cell>
                                <Table.Cell>{ele['Mode_Formation']}</Table.Cell>
                                <Table.Cell>{ele['Code_Module']}</Table.Cell>
                                <Table.Cell>{ele['Module']}</Table.Cell>
                                <Table.Cell>{ele['Régional']==='O' ? 'Oui' : 'Non'}</Table.Cell>
                                <Table.Cell>{ele['MHP_S1']}</Table.Cell>
                                <Table.Cell>{ele['MHSYN_S1']}</Table.Cell>
                                <Table.Cell>{ele['MH_Totale_S1']}</Table.Cell>
                                <Table.Cell>{ele['MHP_Realisee_Présentiel']}</Table.Cell>
                                <Table.Cell>{ele['MH_Réalisee_Sync']}</Table.Cell>
                                <Table.Cell>{ele['MH_Réalisée_Globale']}</Table.Cell>
                            </tr>
                        ))
                    }
                </Table.Body>
            </Table>
        </div>
        </Card>
    </div>
  )
}
