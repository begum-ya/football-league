import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { getStandings, getFixtures, getPredictions, getCurrentWeek, playAllWeek, playWeek, resetData } from "../actions";
import {Fixture,Prediction,TeamStats} from 'types'


const Simulation: React.FC = () => {
  const [standings, setStandings] = useState<TeamStats[]>([]);
  const [fixtures, setFixtures] = useState<Fixture[]>([]);
  const [championshipPredictions, setChampionshipPredictions] = useState<Prediction[]>([]);
  const [currentWeek, setCurrentWeek] = useState<number | null>(null);
  const [weekTitle, setWeekTitle] = useState<string>("Week");
  const [loading, setLoading] = useState<boolean>(true);
  const [predictionMessage, setPredictionMessage] = useState<string | null>(null); 
  const [finalWeeklyResults, setFinalWeeklyResults] = useState<{ week: number; games: { week:number; home: string; away: string; homeGoals: number; awayGoals: number }[] }[]>([]);
  const [showFinalResults, setShowFinalResults] = useState<boolean>(false); 
  
  const navigate = useNavigate();

  useEffect(() => {
    loadAllData();
  }, []);

  const loadAllData = async () => {
    setLoading(true);
    await Promise.all([fetchStandings(), fetchFixtures(), fetchPredictions(), fetchCurrentWeek()]);
    setLoading(false);
  };

  const fetchStandings = async () => {
    try {
      const response = await getStandings();
      setStandings(response.data.data);
    } catch (error) {
      console.error("Error fetching standings:", error);
    }
  };

  const fetchCurrentWeek = async () => {
    try {
      const response = await getCurrentWeek();
      if(response.data.success){
        setCurrentWeek(response.data.current_week);
        setWeekTitle(response.data.week_title);
      }else{
        setCurrentWeek(null);
        setWeekTitle("No Weeks Remaining");

  
      }
      
    } catch (error) {
      console.error("Error fetching current week:", error);
    }
  };

  const fetchFixtures = async () => {
    try {
      const response = await getFixtures();
      setFixtures(response.data.data);
    } catch (error) {
      console.error("Error fetching fixtures:", error);
    }
  };

  const fetchPredictions = async () => {
    try {
      const response = await getPredictions();
      console.log("Fetched predictions:", response.data); 
  
      if (response.data.success) {
        setChampionshipPredictions(response.data.data); 
        setPredictionMessage(null); 
      } else {
        console.log("false data")
        setChampionshipPredictions([]);
        setPredictionMessage(response.data.message || "No predictions available."); 
      }
    } catch (error) {
      console.error("Error fetching predictions:", error);
      setPredictionMessage("An error occurred while fetching predictions.");
    }
  };

  const handlePlayAllWeeks = async () => {

    setFinalWeeklyResults([]); 
    setShowFinalResults(false);
    const allWeeksResults: { week: number; games: { week:number; home: string; away: string; homeGoals: number; awayGoals: number }[] }[] = [];

    if (fixtures.length === 0) return;
    setLoading(true);
    try {
      await playAllWeek();
      await loadAllData();

      const fixturesResponse = await getFixtures();
      const newFixtures = fixturesResponse.data.data;
      const weekGames = newFixtures
      .reduce((acc, fixture) => {
        fixture.games.forEach((game) => {
          acc.push({
            home: game.home_team.name,
            away: game.away_team.name,
            homeGoals: game.home_score,
            awayGoals: game.away_score,
            week:fixture.week
          });
        });
        return acc;
      }, [] as { week:number;home: string; away: string; homeGoals: number; awayGoals: number }[]);

      allWeeksResults.push({ week: currentWeek as number, games: weekGames });

    setFinalWeeklyResults(allWeeksResults);
    setShowFinalResults(true);
    } catch (error) {
      console.error("Error playing all weeks:", error);
    }
    setLoading(false);
  };

  const handlePlayNextWeek = async () => {
    if (currentWeek === null || fixtures.length === 0) return;
    setLoading(true);
    try {
      await playWeek(currentWeek);
      await loadAllData();
    } catch (error) {
      console.error("Error playing next week:", error);
    }
    setLoading(false);
  };

  const handleReset = async () => {
    setLoading(true);
    try {
      await resetData();
      setCurrentWeek(null);
      navigate("/");
      await loadAllData();
    } catch (error) {
      console.error("Error resetting simulation:", error);
    }
    setLoading(false);
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <h1 className="text-4xl font-bold text-gray-600">Loading...</h1>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-white p-8 flex flex-col items-center">
      <h1 className="text-3xl font-bold text-center mb-6">Simulation</h1>

      <div className="grid grid-cols-12 gap-8 w-full">
        {/* Team Standings - Wider */}
        <div className="p-6 rounded-lg col-span-6 border border-gray-300">
          <h2 className="text-xl font-bold text-center mb-4">Standings</h2>
          {standings.length > 0 ? (
            <table className="w-full border-collapse">
              <thead>
                <tr className="bg-gray-800 text-white">
                  <th className="p-3">Team Name</th>
                  <th className="p-3">P</th>
                  <th className="p-3">W</th>
                  <th className="p-3">D</th>
                  <th className="p-3">L</th>
                  <th className="p-3">GD</th>
                </tr>
              </thead>
              <tbody>
                {standings.map((team, index) => (
                  <tr key={index} className="border-b border-gray-300 text-center">
                    <td className="p-3">{team.team.name}</td>
                    <td className="p-3">{team.points}</td>
                    <td className="p-3">{team.won}</td>
                    <td className="p-3">{team.draw}</td>
                    <td className="p-3">{team.lose}</td>
                    <td className="p-3">{team.goal_difference}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <p className="text-gray-500 text-center">No standings available</p>
          )}
        </div>

        {/* Fixtures */}
        <div className="p-6 rounded-lg col-span-3 border border-gray-300">
          <h2 className="text-xl font-bold text-center mb-4">{weekTitle}</h2>
          {fixtures.length > 0 ? (
            <table className="w-full border-collapse">
              <tbody>
                {fixtures
                  .filter((match) => match.week === currentWeek)
                  .map((match) =>
                    match.games.map((game, index) => (
                      <tr key={index} className="border-b text-center">
                        <td className="p-3">{game.home_team.name} - {game.away_team.name}</td>
                      </tr>
                    ))
                  )}
              </tbody>
            </table>
          ) : (
            <p className="text-gray-500 text-center">No fixtures available</p>
          )}
        </div>

        {/* Championship Predictions */}
        <div className="p-6 rounded-lg col-span-3 border border-gray-300">
          <h2 className="text-xl font-bold text-center mb-4">Championship Predictions</h2>
          {championshipPredictions.length > 0 ? (
            <table className="w-full border-collapse">
              <tbody>
                {championshipPredictions.map((team, index) => (
                  <tr key={index} className="border-b border-gray-300 text-center">
                    <td className="p-3">{team.team}: {team.championship_probability}%</td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <p className="text-gray-500 text-center">{predictionMessage}</p>
          )}
        </div>

      </div>
  
      <div className={`mt-8 w-full transition-opacity duration-500 ${showFinalResults ? "opacity-100" : "opacity-0 hidden"}`}>
        <h2 className="text-2xl font-bold text-center mb-4">Week-by-Week Results</h2>

        {finalWeeklyResults.length === 0 ? (
          <p className="text-gray-500 text-center">All weeks must be played to see results.</p>
        ) : (
          finalWeeklyResults.map((weekData) => (
            <div key={weekData.week} className="border rounded-lg p-4 mb-6">
            
              <table className="w-full mt-2 border-collapse">
                <thead>
                  <tr className="bg-gray-800 text-white text-center">
                    <th className="p-3">Week</th>
                    <th className="p-3">Home Team</th>
                    <th className="p-3">Score</th>
                    <th className="p-3">Away Team</th>
                  </tr>
                </thead>
                <tbody>
                  {weekData.games.map((game, index) => (
                    <tr key={index} className="border-b text-center">
                    <td className="p-3">Week {game.week}</td>
                      <td className="p-3">{game.home}</td>
                      <td className="p-3 font-bold">{game.homeGoals} - {game.awayGoals}</td>
                      <td className="p-3">{game.away}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ))
        )}
    </div>
     
      {/* Control Buttons */}
      <div className="flex space-x-16 mt-16">
        <button 
          onClick={handlePlayAllWeeks}
          className={`px-12 py-4 rounded-lg ${fixtures.length === 0 || currentWeek == null ? "bg-gray-400 text-gray-600 cursor-not-allowed" : "bg-blue-500 text-white hover:bg-blue-600 cursor-pointer"}`}
          disabled={fixtures.length === 0}
        >
          Play All Weeks
        </button>
        <button 
          onClick={handlePlayNextWeek}
          className={`px-12 py-4 rounded-lg ${fixtures.length === 0 || currentWeek == null ? "bg-gray-400 text-gray-600 cursor-not-allowed" : "bg-blue-500 text-white hover:bg-blue-600 cursor-pointer"}`}
          disabled={fixtures.length === 0}
        >
          Play Next Week
        </button>
        <button
          onClick={handleReset}
          className="bg-red-500 text-white px-12 py-4 rounded-lg hover:bg-red-600 cursor-pointer transition duration-200"
          >
          Reset Data
          </button>
      </div>
    </div>
  );
};

export default Simulation;
