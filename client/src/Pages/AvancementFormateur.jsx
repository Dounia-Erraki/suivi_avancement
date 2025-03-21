import { Card, Spinner, Table } from 'flowbite-react';
import React, { useEffect, useState } from 'react'
import { HiInformationCircle } from 'react-icons/hi';
import { useNavigate } from 'react-router-dom';

export default function AvancementFormateur() {
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
            const response = await fetch("http://127.0.0.1:8000/api/avancementFormateur");

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
                    d['Nom_Prenom_Formateur'].toLowerCase().includes(searched.toLowerCase())
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

  return (
    <div className='mx-auto p-4'>
        <Card>
        <h2 className='text-2xl font-bold text-black mb-4'>
            Etat d'avancement par Formateur 
        </h2>
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
                            id="search"
                            value={searched}
                            onChange={e=>setSearched(e.target.value)}
                            class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Recherche Par Formateur"
                            required
                        />

                    </div>
                </form>
        <div className='overflow-x-auto'>
            <Table striped  className='text-black text-xs'>
                <Table.Head className="bg-gray-100">
                        <Table.HeadCell>Mle Formateur</Table.HeadCell>
                        <Table.HeadCell>Nom & Prénom Formateur</Table.HeadCell>
                        <Table.HeadCell>Filière</Table.HeadCell>
                        <Table.HeadCell>Type de Formation</Table.HeadCell>
                        <Table.HeadCell>Groupe</Table.HeadCell>
                        <Table.HeadCell>Année de Formation</Table.HeadCell>
                        <Table.HeadCell>Mode</Table.HeadCell>
                        <Table.HeadCell>Code Module</Table.HeadCell>
                        <Table.HeadCell>Module</Table.HeadCell>
                        <Table.HeadCell>MHP Totale</Table.HeadCell>
                        <Table.HeadCell>MHSYN Totale</Table.HeadCell>
                        <Table.HeadCell>MH Totale (1)</Table.HeadCell>
                        <Table.HeadCell>MHP Réalisée</Table.HeadCell>
                        <Table.HeadCell>MHSYN Réalisée</Table.HeadCell>
                        <Table.HeadCell>MH Totale Réalisée (2)</Table.HeadCell>
                        <Table.HeadCell>% de Réalisation</Table.HeadCell>
                </Table.Head>
                <Table.Body className="divide-y text-center">
                    {filteredData?.map((ele,ind)=>(
                        <Table.Row key={ind} className='bg-white'>
                            <Table.Cell>{ele['Mle_Formateur']}</Table.Cell>
                            <Table.Cell>{ele['Nom_Prenom_Formateur']}</Table.Cell>
                            <Table.Cell>{ele['Filière']}</Table.Cell>
                            <Table.Cell>{ele['Type_de_Formation']}</Table.Cell>
                            <Table.Cell>{ele['Groupe']}</Table.Cell>
                            <Table.Cell>{ele['Année_de_Formation']}</Table.Cell>
                            <Table.Cell>{ele['Mode']}</Table.Cell>
                            <Table.Cell>{ele['Code_Module']}</Table.Cell>
                            <Table.Cell>{ele['Module']}</Table.Cell>
                            <Table.Cell>{ele['MHP_Totale']}</Table.Cell>
                            <Table.Cell>{ele['MHSYN_Totale']}</Table.Cell>
                            <Table.Cell>{ele['MHP_Totale']+ele['MHSYN_Totale']}</Table.Cell>
                            <Table.Cell>{ele['MHP_Réalisée']}</Table.Cell>
                            <Table.Cell>{ele['MHSYN_Réalisée']}</Table.Cell>
                            <Table.Cell>{ele['MHP_Réalisée'] + ele['MHSYN_Réalisée']}</Table.Cell>
                            <Table.Cell >
                                <span className={`font-bold ${((ele['MHP_Réalisée'] + ele['MHSYN_Réalisée']) / (ele['MHP_Totale'] + ele['MHSYN_Totale']) * 100) >= 80 ? 'text-green-600' :
                                        ((ele['MHP_Réalisée'] + ele['MHSYN_Réalisée']) / (ele['MHP_Totale'] + ele['MHSYN_Totale']) * 100) >= 60 ? 'text-yellow-600' : 'text-red-600'}`}>
                                {((ele['MHP_Réalisée'] + ele['MHSYN_Réalisée']) / (ele['MHP_Totale'] + ele['MHSYN_Totale']) * 100).toFixed(2)} %
                            </span>
                            </Table.Cell>
                        </Table.Row>
                    ))}
                </Table.Body>
            </Table>
        </div>
        </Card>
    </div>
  )
}
