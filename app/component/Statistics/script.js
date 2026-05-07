let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

const Statistics = {
  format: function(stats) {
    // Replace placeholders with actual data
    let html = template
      .replace('{{total_profiles}}', stats.total_profiles || 0)
      .replace('{{total_movies}}', stats.total_movies || 0)
      .replace('{{average_favorites}}', stats.average_favorites_per_profile || 0)
      .replace('{{most_favorited_title}}', stats.most_favorited_movie?.title || 'Aucun')
      .replace('{{most_favorited_count}}', stats.most_favorited_movie?.favorites_count || 0)
      .replace('{{most_popular_category}}', stats.most_popular_category?.category_name || 'Aucune')
      .replace('{{most_popular_count}}', stats.most_popular_category?.favorites_count || 0);

    return html;
  }
};

export default Statistics;