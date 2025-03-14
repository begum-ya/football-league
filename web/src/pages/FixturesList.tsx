
import React, { useEffect, useState } from "react";
import { getFixtures } from "../actions";
import { Fixture } from "types";
import { useNavigate } from "react-router-dom";

const FixturesList: React.FC = () => {
  const [fixtures, setFixtures] = useState<Fixture[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const navigate = useNavigate();

  useEffect(() => {
    getFixtures()
      .then((response) => {
        console.log("fixtures");
        console.log(fixtures)
        setFixtures(response.data.data || []); 
      })
      .catch((error) => console.error("Error fetching fixtures:", error))
      .finally(() => setLoading(false)); 
  }, []);

  const handleStartSimulation = () => {
    navigate("/simulation"); 
  };
  
  return (
    <div className="p-6">
      <h1 className="text-3xl font-bold text-center">Generated Fixtures</h1>

      {loading ? (
        <div className="text-center text-gray-500 mt-6">Loading fixtures...</div>
      ) : fixtures.length === 0 ? (

        <div className="text-center text-red-500 mt-6 text-lg">
          No fixtures available. Please generate fixtures first.
        </div>
      ) : (

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
          {fixtures.map((fixture) => (
            <div key={fixture.id} className="border rounded-lg">
              <h2 className="text-lg font-semibold bg-gray-800 text-white p-2 rounded-t-lg">
                Week {fixture.week}
              </h2>
              <table className="w-full mt-2">
                <tbody>
                  {fixture.games.map((game, index) => (
                    <tr key={index} className="border-b">
                      <td className="py-2 text-center">{game.home_team.name}</td>
                      <td className="py-2 text-center">-</td>
                      <td className="py-2 text-center">{game.away_team.name}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ))}
        </div>
      )}

      <div 
       className={`${fixtures.length === 0  ? "hidden" : "mt-6 flex justify-start"}`}>
       
        <button 
          onClick={handleStartSimulation}
          className={"bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"}
        >
          Start Simulation
        </button>
      </div>
    </div>
  );
};

export default FixturesList;