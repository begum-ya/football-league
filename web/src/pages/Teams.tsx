import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { getCsrfCookie,getTeams,generateFixtures } from "../actions";
import { Team } from "types";

const Teams: React.FC = () => {
  const [teams, setTeams] = useState<Team[]>([]);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const navigate = useNavigate();

  useEffect(() => {
    getTeams()
      .then((response) => {
        if (Array.isArray(response.data.data)) {
          setTeams(response.data.data);
        } else {
          console.error("Unexpected API response format", response.data);
        }
      })
      .catch((error) => console.error("Error fetching teams:", error));
  }, []);

  const handleGenerateFixtures = async () => {
    setErrorMessage(null);
    // if (teams.length % 2 !== 0) {
    //   setErrorMessage("The number of teams must be even to generate fixtures.");
    //   return;
    // }
    try {
      await getCsrfCookie();
      await generateFixtures();
      navigate("/fixtures");
    } catch (error) {
      console.error("Error generating fixtures:", error);
    }
  };

  return (
    <div className="flex flex-col items-center justify-center min-h-screen bg-white">
      <h1 className="text-3xl font-bold text-gray-800 mb-6">Tournament Teams</h1>
      {errorMessage && (
        <div
          className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
          role="alert"
        >
          <strong className="font-bold">Error!</strong>
          <span className="block sm:inline">{errorMessage}</span>
        </div>
      )}
      <div className="w-1/3 bg-white shadow-lg rounded-lg">
        <table className="w-full border-collapse">
          <thead>
            <tr className="bg-gray-800 text-white">
              <th className="py-3 px-6 text-left">Team Name</th>
            </tr>
          </thead>
          <tbody>
            {teams.map((team) => (
              <tr key={team.id} className="border-b">
                <td className="py-3 px-6 text-left">{team.name}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      <div className="mt-6">
        <button
          onClick={handleGenerateFixtures}
          className="bg-blue-500 text-white hover:bg-blue-600 cursor-pointer text-white px-6 py-3 rounded-lg font-bold transition-all"
        >
          Generate Fixtures
        </button>
      </div>
    </div>
  );
};

export default Teams;