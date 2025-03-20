import React, { useEffect, useState } from 'react';
import { useNavigate } from "react-router-dom";
import { Table, Card, Spinner, Alert, Button } from "flowbite-react";
import { HiInformationCircle } from "react-icons/hi";


export default function Affectation() {
  const [excelData, setExcelData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const navigate = useNavigate();
  
  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const response = await fetch("http://127.0.0.1:8000/api/AffectationController");
        
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
    <div className="mx-auto p-4">
      <Card>
        <h2 className="text-2xl font-bold text-black mb-4">Etat d'affectation des modules</h2>
        
        <div className="overflow-x-auto">
          <Table striped hoverable className="text-black text-xs">
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
              <Table.HeadCell>MHP S1</Table.HeadCell>
              <Table.HeadCell>MHSYN S1</Table.HeadCell>
              <Table.HeadCell>MH Totale S1</Table.HeadCell>
              <Table.HeadCell>MHP S2</Table.HeadCell>
              <Table.HeadCell>MHSYN S2</Table.HeadCell>
              <Table.HeadCell>MH Totale S2</Table.HeadCell>
              <Table.HeadCell>MHP Totale</Table.HeadCell>
              <Table.HeadCell>MHSYN Totale</Table.HeadCell>
              <Table.HeadCell>MH Totale</Table.HeadCell>
            </Table.Head>

            <Table.Body className="divide-y text-center">
              {excelData.map((data, index) => (
                <Table.Row key={index} className="bg-white">
                  <Table.Cell className=' text-left'>{data.mle_formateur}</Table.Cell>
                  <Table.Cell className="whitespace-nowrap text-left">{data.nom_formateur}</Table.Cell>
                  <Table.Cell className=' text-left'>{data.nom_filiere}</Table.Cell>
                  <Table.Cell>{data.type_formation}</Table.Cell>
                  <Table.Cell>{data.nom_groupe}</Table.Cell>
                  <Table.Cell>{data.annee_formation}</Table.Cell>
                  <Table.Cell>{data.mode_formation}</Table.Cell>
                  <Table.Cell>{data.code_module}</Table.Cell>
                  <Table.Cell className=' text-left'>{data.nom_module}</Table.Cell>
                  <Table.Cell>{data.mhp_S1}</Table.Cell>
                  <Table.Cell>{data.mhsyn_S1}</Table.Cell>
                  <Table.Cell className=' font-medium'>{data.mhp_S1 + data.mhsyn_S1}</Table.Cell>
                  <Table.Cell>{data.mhp_S2}</Table.Cell>
                  <Table.Cell>{data.mhsyn_S2}</Table.Cell>
                  <Table.Cell className=' font-medium'>{data.mhp_S2 + data.mhsyn_S2}</Table.Cell>
                  <Table.Cell className=' font-medium'>{data.mhp_S1 + data.mhp_S2}</Table.Cell>
                  <Table.Cell className=' font-medium'>{data.mhsyn_S1 + data.mhsyn_S2}</Table.Cell>
                  <Table.Cell className=' font-medium'>{data.mhp_S1 + data.mhp_S2 + data.mhsyn_S1 + data.mhsyn_S2}</Table.Cell>
                </Table.Row>
              ))}
            </Table.Body>
          </Table>
        </div>
      </Card>
    </div>
  );
}